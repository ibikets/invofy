<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEstimateRequest;
use App\Http\Requests\UpdateEstimateRequest;
use App\Models\Billing\Estimate;
use App\Models\Billing\EstimateItem;
use App\Models\Finance\TaxProfile;
use App\Services\Numbers\NumberGenerator;
use App\Services\Tax\TaxCalculator;
use App\Support\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimatesController extends Controller
{
    public function index(Request $request)
    {
        $q = Estimate::query()->withCount('items');

        if ($s = $request->query('q')) {
            $q->where('number','ilike',"%{$s}%");
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        return response()->json($q->orderByDesc('created_at')->paginate(20));
    }

    public function show(Estimate $estimate)
    {
        $estimate->load('items');
        return response()->json($estimate);
    }

    public function store(StoreEstimateRequest $request, CurrentTenant $ct, NumberGenerator $nums, TaxCalculator $taxer)
    {
        $tenant = $ct->tenant();
        abort_if(!$tenant, 404);

        $payload = $request->validated();
        $items = $payload['items'];
        unset($payload['items']);

        return DB::transaction(function () use ($tenant, $payload, $items, $nums, $taxer) {
            // document number
            $number = $nums->next($tenant->id, 'estimate', 'EST-');

            $estimate = Estimate::create(array_merge($payload, [
                'tenant_id' => $tenant->id,
                'number'    => $number,
                'status'    => 'draft',
            ]));

            [$subTotal, $discountTotal, $taxTotal, $grandTotal] = [0,0,0,0];

            foreach ($items as $i => $row) {
                $profile = isset($row['tax_profile_id'])
                    ? TaxProfile::find($row['tax_profile_id'])
                    : null;

                $taxes = $taxer->taxesFromProfile($profile);
                $calc = $taxer->line(
                    unitPrice: (float)($row['unit_price'] ?? 0),
                    qty: (float)($row['qty'] ?? 1),
                    discount: (float)($row['discount'] ?? 0),
                    taxes: $taxes
                );

                $line = EstimateItem::create([
                    'tenant_id'      => $tenant->id,
                    'estimate_id'    => $estimate->id,
                    'item_id'        => $row['item_id'] ?? null,
                    'name'           => $row['name'],
                    'description'    => $row['description'] ?? null,
                    'qty'            => $row['qty'] ?? 1,
                    'unit'           => $row['unit'] ?? null,
                    'unit_price'     => $row['unit_price'] ?? 0,
                    'discount'       => $row['discount'] ?? 0,
                    'tax_profile_id' => $row['tax_profile_id'] ?? null,
                    'taxes_cache'    => $calc['taxes'],
                    'line_subtotal'  => $calc['subtotal'],
                    'line_tax_total' => $calc['tax_total'],
                    'line_total'     => $calc['total'],
                    'sort_order'     => $row['sort_order'] ?? $i,
                ]);

                $subTotal     += (float)$line->line_subtotal;
                $discountTotal+= (float)($row['discount'] ?? 0);
                $taxTotal     += (float)$line->line_tax_total;
                $grandTotal   += (float)$line->line_total;
            }

            $estimate->update([
                'sub_total'      => round($subTotal, 2),
                'discount_total' => round($discountTotal, 2),
                'tax_total'      => round($taxTotal, 2),
                'total'          => round($grandTotal, 2),
            ]);

            return response()->json($estimate->fresh('items'), 201);
        });
    }

    public function update(UpdateEstimateRequest $request, Estimate $estimate, CurrentTenant $ct, TaxCalculator $taxer)
    {
        $tenant = $ct->tenant();
        abort_if(!$tenant, 404);

        $payload = $request->validated();
        $items = $payload['items'] ?? null;
        unset($payload['items']);

        return DB::transaction(function () use ($estimate, $payload, $items, $taxer) {
            if (!empty($payload)) {
                $estimate->update($payload);
            }

            if (is_array($items)) {
                // Clear and re-insert simplistic approach for v1
                $estimate->items()->delete();

                [$subTotal, $discountTotal, $taxTotal, $grandTotal] = [0,0,0,0];

                foreach ($items as $i => $row) {
                    $profile = isset($row['tax_profile_id'])
                        ? \App\Models\Finance\TaxProfile::find($row['tax_profile_id'])
                        : null;

                    $taxes = $taxer->taxesFromProfile($profile);
                    $calc = $taxer->line(
                        unitPrice: (float)($row['unit_price'] ?? 0),
                        qty: (float)($row['qty'] ?? 1),
                        discount: (float)($row['discount'] ?? 0),
                        taxes: $taxes
                    );

                    $line = \App\Models\Billing\EstimateItem::create([
                        'tenant_id'      => $estimate->tenant_id,
                        'estimate_id'    => $estimate->id,
                        'item_id'        => $row['item_id'] ?? null,
                        'name'           => $row['name'],
                        'description'    => $row['description'] ?? null,
                        'qty'            => $row['qty'] ?? 1,
                        'unit'           => $row['unit'] ?? null,
                        'unit_price'     => $row['unit_price'] ?? 0,
                        'discount'       => $row['discount'] ?? 0,
                        'tax_profile_id' => $row['tax_profile_id'] ?? null,
                        'taxes_cache'    => $calc['taxes'],
                        'line_subtotal'  => $calc['subtotal'],
                        'line_tax_total' => $calc['tax_total'],
                        'line_total'     => $calc['total'],
                        'sort_order'     => $row['sort_order'] ?? $i,
                    ]);

                    $subTotal     += (float)$line->line_subtotal;
                    $discountTotal+= (float)($row['discount'] ?? 0);
                    $taxTotal     += (float)$line->line_tax_total;
                    $grandTotal   += (float)$line->line_total;
                }

                $estimate->update([
                    'sub_total'      => round($subTotal, 2),
                    'discount_total' => round($discountTotal, 2),
                    'tax_total'      => round($taxTotal, 2),
                    'total'          => round($grandTotal, 2),
                ]);
            }

            return response()->json($estimate->fresh('items'));
        });
    }

    public function setStatus(Request $request, Estimate $estimate)
    {
        $request->validate(['status' => 'required|in:draft,sent,accepted,declined,expired']);
        $estimate->update(['status' => $request->string('status')]);
        return response()->json($estimate);
    }
}

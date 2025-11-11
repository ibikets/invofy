<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceItem;
use App\Models\Finance\TaxProfile;
use App\Services\Numbers\NumberGenerator;
use App\Services\Tax\TaxCalculator;
use App\Support\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::query()->withCount('items');

        if ($s = $request->query('q')) {
            $q->where('number','ilike',"%{$s}%");
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        return response()->json($q->orderByDesc('created_at')->paginate(20));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return response()->json($invoice);
    }

    public function store(StoreInvoiceRequest $request, CurrentTenant $ct, NumberGenerator $nums, TaxCalculator $taxer)
    {
        $tenant = $ct->tenant();
        abort_if(!$tenant, 404);

        $payload = $request->validated();
        $items = $payload['items'];
        unset($payload['items']);

        return DB::transaction(function () use ($tenant, $payload, $items, $nums, $taxer) {
            $number = $nums->next($tenant->id, 'invoice', 'INV-');

            $invoice = Invoice::create(array_merge($payload, [
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

                $line = InvoiceItem::create([
                    'tenant_id'      => $tenant->id,
                    'invoice_id'     => $invoice->id,
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

            $invoice->update([
                'sub_total'      => round($subTotal, 2),
                'discount_total' => round($discountTotal, 2),
                'tax_total'      => round($taxTotal, 2),
                'total'          => round($grandTotal, 2),
                'balance'        => round($grandTotal, 2), // full balance at creation
            ]);

            return response()->json($invoice->fresh('items'), 201);
        });
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice, TaxCalculator $taxer)
    {
        $payload = $request->validated();
        $items = $payload['items'] ?? null;
        unset($payload['items']);

        return DB::transaction(function () use ($invoice, $payload, $items, $taxer) {
            if (!empty($payload)) {
                $invoice->update($payload);
            }

            if (is_array($items)) {
                $invoice->items()->delete();

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

                    $line = \App\Models\Billing\InvoiceItem::create([
                        'tenant_id'      => $invoice->tenant_id,
                        'invoice_id'     => $invoice->id,
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

                $invoice->update([
                    'sub_total'      => round($subTotal, 2),
                    'discount_total' => round($discountTotal, 2),
                    'tax_total'      => round($taxTotal, 2),
                    'total'          => round($grandTotal, 2),
                    // leave balance untouched here; payments will adjust in Phase 7
                ]);
            }

            return response()->json($invoice->fresh('items'));
        });
    }

    public function setStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,sent,partially_paid,paid,overdue,cancelled']);
        $invoice->update(['status' => $request->string('status')]);
        return response()->json($invoice);
    }
}

<?php

namespace App\Services\Tax;

use App\Models\Finance\Tax;
use App\Models\Finance\TaxProfile;

class TaxCalculator
{
    /**
     * Calculate taxes for a single line.
     * @param float $unitPrice  price per unit (as entered)
     * @param float $qty
     * @param float $discount   per-line absolute discount (optional)
     * @param Tax[] $taxes      ordered list (respect compound order)
     * @return array { subtotal, taxes: [[id,name,amount,rate]], tax_total, total, effective_unit_price }
     */
    public function line(float $unitPrice, float $qty, float $discount, array $taxes): array
    {
        $base = max(0, ($unitPrice * $qty) - $discount);
        $subtotal = $base;
        $taxRows = [];
        $runningBase = $base;

        // If any tax is inclusive, back it out first (applies when price includes tax)
        // Simple approach: if any inclusive tax present, assume the entered price is tax-inclusive for those taxes.
        foreach ($taxes as $tax) {
            if ($tax->inclusive) {
                // Remove inclusive tax from running base to get net
                $rate = (float)$tax->rate / 100.0;
                // net = price / (1 + rate) when single inclusive; with multiple inclusives, approximate sequentially
                $net = $runningBase / (1 + $rate);
                $inclusiveAmount = $runningBase - $net;
                $taxRows[] = ['id'=>$tax->id,'name'=>$tax->name,'rate'=>$tax->rate,'amount'=>round($inclusiveAmount,2)];
                $runningBase = $net; // reduce base for subsequent (exclusive/compound) taxes
            }
        }

        // Now apply exclusive/compound taxes in order
        $exclusiveBase = $runningBase;
        foreach ($taxes as $tax) {
            if (! $tax->inclusive) {
                $rate = (float)$tax->rate / 100.0;
                $applyBase = $tax->compound ? $exclusiveBase + $this->sumTaxAmounts($taxRows) : $exclusiveBase;
                $amount = round($applyBase * $rate, 2);
                $taxRows[] = ['id'=>$tax->id,'name'=>$tax->name,'rate'=>$tax->rate,'amount'=>$amount];
            }
        }

        $taxTotal = round($this->sumTaxAmounts($taxRows), 2);
        $total = round($runningBase + $taxTotal, 2);

        // Effective unit price (post-inclusives): useful when displaying per-unit net
        $effectiveUnitPrice = $qty > 0 ? round($runningBase / $qty, 2) : $unitPrice;

        return [
            'subtotal' => round($subtotal, 2),
            'taxes' => $taxRows,
            'tax_total' => $taxTotal,
            'total' => $total,
            'effective_unit_price' => $effectiveUnitPrice,
        ];
    }

    public function taxesFromProfile(?TaxProfile $profile): array
    {
        if (! $profile || empty($profile->tax_ids)) return [];
        return Tax::query()->whereIn('id', $profile->tax_ids)->orderByRaw(
            "array_position(ARRAY[?]::text[], id::text)", [implode(',', $profile->tax_ids)]
        )->get()->all();
    }

    private function sumTaxAmounts(array $rows): float
    {
        return array_reduce($rows, fn($c,$r) => $c + (float)$r['amount'], 0.0);
    }
}

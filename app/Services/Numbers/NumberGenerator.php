<?php

namespace App\Services\Numbers;

use App\Models\Settings\NumberSequence;
use Illuminate\Support\Facades\DB;

class NumberGenerator
{
    public function next(string $tenantId, string $entity, string $defaultPrefix): string
    {
        return DB::transaction(function () use ($tenantId, $entity, $defaultPrefix) {
            $seq = NumberSequence::firstOrCreate(
                ['tenant_id' => $tenantId, 'entity_type' => $entity],
                ['prefix' => $defaultPrefix, 'padding' => 5, 'next_number' => 1]
            );

            // lock row to prevent race
            $locked = NumberSequence::whereKey($seq->getKey())->lockForUpdate()->first();
            $n = $locked->next_number;
            $locked->next_number = $n + 1;
            $locked->save();

            return $locked->prefix . str_pad((string)$n, $locked->padding, '0', STR_PAD_LEFT);
        });
    }
}

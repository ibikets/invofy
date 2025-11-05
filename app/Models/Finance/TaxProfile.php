<?php

namespace App\Models\Finance;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class TaxProfile extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'tax_profiles';

    protected $fillable = [
        'tenant_id','name','tax_ids','active',
    ];

    protected $casts = [
        'tax_ids' => 'array',
        'active' => 'bool',
    ];

    public function taxes()
    {
        return \App\Models\Finance\Tax::query()
            ->whereIn('id', $this->tax_ids ?? [])
            ->orderByRaw("array_position(ARRAY[?]::text[], id::text)", [implode(',', $this->tax_ids ?? [])]);
    }
}

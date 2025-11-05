<?php

namespace App\Models\Finance;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'taxes';

    protected $fillable = [
        'tenant_id','name','rate','compound','inclusive','active',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'compound' => 'bool',
        'inclusive' => 'bool',
        'active' => 'bool',
    ];
}

<?php

namespace App\Models\Directory;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'customers';

    protected $fillable = [
        'tenant_id',
        'display_name',
        'email',
        'emails',
        'phone',
        'currency',
        'tax_exempt',
        'billing_address',
        'shipping_address',
        'custom_fields',
        'status',
    ];

    protected $casts = [
        'emails' => 'array',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'custom_fields' => 'array',
        'tax_exempt' => 'bool',
    ];
}

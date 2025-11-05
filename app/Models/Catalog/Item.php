<?php

namespace App\Models\Catalog;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'items';

    protected $fillable = [
        'tenant_id','type','sku','name','description','unit',
        'default_price','default_discount','tax_profile_id','custom_fields','active',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'default_price' => 'decimal:2',
        'default_discount' => 'decimal:2',
        'active' => 'bool',
    ];

    public function taxProfile()
    {
        return $this->belongsTo(\App\Models\Finance\TaxProfile::class, 'tax_profile_id');
    }
}

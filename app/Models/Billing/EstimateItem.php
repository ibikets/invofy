<?php

namespace App\Models\Billing;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Estimate extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'estimates';

    protected $fillable = [
        'tenant_id','number','customer_id','issue_date','expiry_date','currency','exchange_rate',
        'sub_total','discount_total','tax_total','total','status','notes','meta',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date'=> 'date',
        'exchange_rate' => 'decimal:6',
        'sub_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'meta' => 'array',
    ];

    public function items() {
        return $this->hasMany(EstimateItem::class);
    }
}

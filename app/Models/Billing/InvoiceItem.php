<?php

namespace App\Models\Billing;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class InvoiceItem extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'invoice_items';

    protected $fillable = [
        'tenant_id','invoice_id','item_id','name','description','qty','unit','unit_price','discount',
        'tax_profile_id','taxes_cache','line_subtotal','line_tax_total','line_total','sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'taxes_cache' => 'array',
        'line_subtotal' => 'decimal:2',
        'line_tax_total' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
}

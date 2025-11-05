<?php

namespace App\Models\Directory;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'vendors';

    protected $fillable = [
        'tenant_id',
        'display_name',
        'email',
        'phone',
        'address',
        'custom_fields',
        'status',
    ];

    protected $casts = [
        'address' => 'array',
        'custom_fields' => 'array',
    ];
}

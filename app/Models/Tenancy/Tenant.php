<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{

    use HasUlids;

    protected $table = 'tenants';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'slug', 'country', 'currency', 'settings', 'plan_id', 'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}

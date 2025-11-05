<?php

namespace App\Models\Settings;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    use HasUlids, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'number_sequences';

    protected $fillable = [
        'tenant_id','entity_type','prefix','padding','next_number',
    ];

    protected $casts = [
        'padding' => 'integer',
        'next_number' => 'integer',
    ];
}

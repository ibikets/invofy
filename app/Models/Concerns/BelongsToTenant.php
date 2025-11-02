<?php

namespace App\Models\Concerns;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (static::hasTenantColumn($model) && empty($model->tenant_id)) {
                $tenantId = app()->bound(CurrentTenant::class) ? app(CurrentTenant::class)->id() : null;
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = auth()->user();
            if ($user && $user->is_super_admin) {
                return;
            }

            $tenantId = app()->bound(CurrentTenant::class) ? app(CurrentTenant::class)->id() : null;
            if ($tenantId && static::hasTenantColumn($builder->getModel())) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
            }
        });
    }

    protected static function hasTenantColumn($model): bool
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        return Schema::hasColumn($model->getTable(), 'tenant_id');
    }
}

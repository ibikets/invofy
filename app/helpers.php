<?php

use App\Tenancy\CurrentTenant;

if (! function_exists('tenant')) {
    function tenant(): ?\App\Domain\Tenancy\Tenant
    {
        return app()->bound(CurrentTenant::class)
            ? app(CurrentTenant::class)->tenant()
            : null;
    }
}

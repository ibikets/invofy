<?php

namespace App\Http\Middleware;

use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeamFromTenant
{
    public function __construct(private PermissionRegistrar $registrar)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = app()->bound(CurrentTenant::class)
            ? app(CurrentTenant::class)->id()
            : null;

        // Tell Spatie which "team" (tenant) we are in for this request
        $this->registrar->setPermissionsTeamId($tenantId);

        return $next($request);
    }
}

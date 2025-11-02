<?php

namespace App\Http\Middleware;

use App\Models\Tenancy\Tenant;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantFromPath
{
    public function handle(Request $request, Closure $next): Response
    {
        // Expecting URLs like: invofy.test/{tenant}/...
        $slug = $request->segment(1); // first path segment

        $tenant = null;

        if ($slug) {
            $tenant = Tenant::where('slug', $slug)->first();
        }

        // Allow super-admin impersonation override (optional)
        if (session()->has('impersonate_tenant_id')) {
            $tenant = Tenant::find(session('impersonate_tenant_id')) ?: $tenant;
        }

        app()->instance(CurrentTenant::class, new CurrentTenant($tenant));

        // If this route must be tenant-only and tenant not found, block:
        // if ($this->isTenantOnlyRoute($request) && ! $tenant) {
        //     abort(404);
        // }

        return $next($request);
    }
}

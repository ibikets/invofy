<?php

namespace App\Http\Middleware;

use App\Domain\Tenancy\Tenant;
use App\Tenancy\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Example host: acme.invofy.test
        $host = $request->getHost();

        $tenant = null;

        // If you're using Valet with subdomains: subdomain.domain.tld
        $parts = explode('.', $host);

        // adjust this logic to your local domain; for "acme.invofy.test" subdomain is "acme"
        if (count($parts) >= 3) {
            $subdomain = $parts[0];

            // You can also store exact domain per tenant and match by domain instead.
            $tenant = Tenant::query()
                ->where('slug', $subdomain)
                ->orWhere('domain', $host)
                ->first();
        }

        // Impersonation override for super-admin support (optional)
        if (session()->has('impersonate_tenant_id')) {
            $tenant = Tenant::find(session('impersonate_tenant_id')) ?: $tenant;
        }

        app()->instance(CurrentTenant::class, new CurrentTenant($tenant));

        // Optionally, block access if tenant not found and this is a tenant-only area
        // if (!$tenant && $this->isTenantOnlyRoute($request)) {
        //     abort(404);
        // }

        return $next($request);
    }
}

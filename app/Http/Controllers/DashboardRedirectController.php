<?php

namespace App\Http\Controllers;

use App\Models\Tenancy\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function toTenant(Request $request): RedirectResponse
    {
        $user = $request->user();

        // If user has a tenant_id, get its slug and route to the tenant dashboard
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                return redirect()->to('/'.$tenant->slug.'/dashboard');
            }
        }

        // Fallback when no tenant_id on user; adjust to your needs (maybe a tenant chooser)
        return redirect()->route('landing');
    }

    public function toTenantSettings(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                return redirect()->to('/'.$tenant->slug.'/settings');
            }
        }

        return redirect()->route('landing');
    }
}

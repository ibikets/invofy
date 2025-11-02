<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenancy\Tenant;
use Illuminate\Http\RedirectResponse;

class ImpersonationController
{
    public function start(string $tenantId): RedirectResponse
    {
        abort_unless(optional(auth()->user())->is_super_admin, 403);

        if (Tenant::find($tenantId)) {
            session(['impersonate_tenant_id' => $tenantId]);
        }

        return back();
    }

    public function stop(): RedirectResponse
    {
        abort_unless(optional(auth()->user())->is_super_admin, 403);

        session()->forget('impersonate_tenant_id');

        return back();
    }
}

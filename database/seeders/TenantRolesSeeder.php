<?php

namespace Database\Seeders;

use App\Models\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class TenantRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Spatie uses team context while seeding
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $roles = ['Owner', 'Admin', 'Finance', 'ReadOnly'];

        // For every tenant, create roles in that tenant context
        Tenant::query()->each(function (Tenant $tenant) use ($registrar, $roles) {
            $registrar->setPermissionsTeamId($tenant->id);

            foreach ($roles as $role) {
                \Spatie\Permission\Models\Role::findOrCreate($role, 'web');
            }

            // Example: give Owner role to the tenant's owner (if you have one)
            $owner = User::where('tenant_id', $tenant->id)->first();
            if ($owner) {
                $owner->syncRoles(['Owner']);
            }
        });

        // reset to null team context after seeding
        $registrar->setPermissionsTeamId(null);
    }
}

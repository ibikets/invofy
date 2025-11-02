<?php

namespace Database\Seeders;

use App\Models\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'acme'],
            [
                'id'       => (string) Str::ulid(),
                'name'     => 'Acme Ltd',
                'country'  => 'NG',
                'currency' => 'NGN',
                'status'   => 'active',
            ]
        );

        if (! User::where('email', 'admin@invofy.local')->exists()) {
            User::create([
                'name'           => 'Invofy SuperAdmin',
                'email'          => 'admin@invofy.local',
                'password'       => Hash::make('password'),
                'is_super_admin' => true,
            ]);
        }

        if (! User::where('email', 'owner@acme.local')->exists()) {
            User::create([
                'name'           => 'Acme Owner',
                'email'          => 'owner@acme.local',
                'password'       => Hash::make('password'),
                'tenant_id'      => $tenant->id,
                'is_super_admin' => false,
            ]);
        }
    }
}

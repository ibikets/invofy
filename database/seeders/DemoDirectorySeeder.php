<?php

namespace Database\Seeders;

use App\Models\Directory\Customer;
use App\Models\Directory\Vendor;
use App\Models\Tenancy\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDirectorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'acme')->first();
        if (! $tenant) return;

        // customers
        Customer::firstOrCreate(
            ['tenant_id' => $tenant->id, 'display_name' => 'Beta Corp'],
            [
                'email' => 'ar@beta.test',
                'phone' => '+2348000000000',
                'currency' => 'NGN',
                'billing_address' => ['line1' => '1 Example Road', 'city' => 'Lagos'],
                'shipping_address'=> ['line1' => '1 Example Road', 'city' => 'Lagos'],
                'custom_fields' => ['account_code' => 'CUST-' . Str::upper(Str::random(6))]
            ]
        );

        // vendors
        Vendor::firstOrCreate(
            ['tenant_id' => $tenant->id, 'display_name' => 'Delta Supplies'],
            [
                'email' => 'ap@delta.test',
                'phone' => '+2348111111111',
                'address' => ['line1' => '2 Market Street', 'city' => 'Abuja'],
                'custom_fields' => ['category' => 'Office']
            ]
        );
    }
}

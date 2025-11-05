<?php

namespace Database\Seeders;

use App\Models\Catalog\Item;
use App\Models\Finance\Tax;
use App\Models\Finance\TaxProfile;
use App\Models\Tenancy\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoFinanceSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug','acme')->first();
        if (! $tenant) return;

        $vat = Tax::firstOrCreate(
            ['tenant_id'=>$tenant->id,'name'=>'VAT 7.5%'],
            ['rate'=>7.5,'compound'=>false,'inclusive'=>false,'active'=>true]
        );

        $profile = TaxProfile::firstOrCreate(
            ['tenant_id'=>$tenant->id,'name'=>'Standard VAT'],
            ['tax_ids'=>[$vat->id], 'active'=>true]
        );

        Item::firstOrCreate(
            ['tenant_id'=>$tenant->id,'name'=>'Consulting Hour'],
            [
                'type'=>'service','sku'=>'CONS-001','unit'=>'hr',
                'default_price'=>50000,'default_discount'=>0,
                'tax_profile_id'=>$profile->id,
                'custom_fields'=>['code'=>'SRV-'.Str::upper(Str::random(5))]
            ]
        );
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('tenant_id');
            $table->string('display_name');
            $table->string('email')->nullable();
            $table->json('emails')->nullable(); // allow multiple
            $table->string('phone')->nullable();
            $table->string('currency', 3)->nullable(); // e.g., NGN, USD
            $table->boolean('tax_exempt')->default(false);
            $table->json('billing_address')->nullable();  // {line1, city, state, country, zip}
            $table->json('shipping_address')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('status')->default('active'); // active|archived
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            // Per-tenant uniqueness (optional — email may be null/duplicate across tenants)
            $table->index(['tenant_id', 'display_name']);
            $table->index(['tenant_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

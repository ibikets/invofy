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
        Schema::create('items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('tenant_id');
            $table->string('type')->default('service'); // service|good
            $table->string('sku')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->nullable();           // e.g. "hr", "pcs"
            $table->decimal('default_price', 18, 2)->default(0);
            $table->decimal('default_discount', 18, 2)->default(0);
            $table->ulid('tax_profile_id')->nullable();
            $table->json('custom_fields')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('tax_profile_id')->references('id')->on('tax_profiles')->nullOnDelete();

            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

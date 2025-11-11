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
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('tenant_id');
            $table->ulid('estimate_id');
            $table->ulid('item_id')->nullable(); // reference catalog item, optional
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('qty', 18, 4)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->ulid('tax_profile_id')->nullable();
            $table->json('taxes_cache')->nullable();   // computed tax rows stored
            $table->decimal('line_subtotal', 18, 2)->default(0);
            $table->decimal('line_tax_total', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();
            $table->index(['tenant_id','estimate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_items');
    }
};

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
        Schema::create('tenants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();               // e.g. "acme"
            $table->string('country')->nullable();
            $table->string('currency', 3)->nullable();      // e.g. "NGN"
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('status')->default('active');    // active|suspended
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};

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
    Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // MBAO, DAILY_SOLO, etc.
        $table->string('name');
        $table->unsignedInteger('price_kes');
        $table->unsignedInteger('duration_minutes')->nullable();
        $table->unsignedInteger('duration_days')->nullable();
        $table->unsignedTinyInteger('max_devices')->default(1);
        $table->string('rate_limit')->nullable();
        $table->unsignedInteger('data_cap_mb')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

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
    if (!Schema::hasTable('payments')) {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('router_id');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->string('provider')->default('mpesa');
            $table->string('reference')->nullable();
            $table->string('phone');
            $table->decimal('amount', 10, 2);
            $table->string('bundle')->nullable();
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('router_id')->references('id')->on('routers')->onDelete('cascade');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

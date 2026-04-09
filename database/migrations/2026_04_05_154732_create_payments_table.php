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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('router_id')->nullable()
                  ->constrained('routers')->nullOnDelete();
            $table->foreignId('voucher_id')->nullable()
                  ->constrained('vouchers')->nullOnDelete();

            $table->string('provider', 32)->default('mpesa'); // mpesa, cash, card, etc.
            $table->string('reference', 64)->unique();        // e.g. Mpesa receipt
            $table->string('phone', 32)->nullable();
            $table->decimal('amount', 10, 2);

           $table->enum('status', ['pending', 'successful', 'failed'])
                  ->default('pending');

            $table->json('payload')->nullable(); // raw callback / metadata

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};


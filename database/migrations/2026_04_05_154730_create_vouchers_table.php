<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();

            $table->string('code', 32)->unique();
            $table->decimal('face_value', 10, 2)->default(0);

            $table->enum('status', ['new', 'reserved', 'used', 'expired', 'disabled'])
                  ->default('new');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->string('customer_phone', 32)->nullable();
            $table->unsignedBigInteger('hotspot_user_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};

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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable();          // e.g. "1HR_5MBPS"
            $table->string('rate_limit')->nullable();    // e.g. "5M/5M"
            $table->unsignedInteger('time_limit_minutes')->nullable(); // null = unlimited
            $table->unsignedBigInteger('data_limit_mb')->nullable();   // null = unlimited
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->unique(['router_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};


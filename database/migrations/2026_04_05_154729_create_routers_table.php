<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('ip_address', 64);
            $table->unsignedInteger('api_port')->default(8728); // RouterOS API port
            $table->string('api_username');
            $table->string('api_password'); // later move to encrypted storage
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};

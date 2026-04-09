<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('time_limit_seconds')->nullable()->after('face_value');
            $table->unsignedBigInteger('data_limit_mb')->nullable()->after('time_limit_seconds');

            $table->unsignedBigInteger('total_time_seconds')->default(0)->after('data_limit_mb');
            $table->unsignedBigInteger('total_data_mb')->default(0)->after('total_time_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'time_limit_seconds',
                'data_limit_mb',
                'total_time_seconds',
                'total_data_mb',
            ]);
        });
    }
};

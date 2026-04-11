<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status', 'payments_status_index');
            $table->index('provider', 'payments_provider_index');
            $table->index('voucher_code', 'payments_voucher_code_index');
            $table->index('router_id', 'payments_router_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_index');
            $table->dropIndex('payments_provider_index');
            $table->dropIndex('payments_voucher_code_index');
            $table->dropIndex('payments_router_id_index');
        });
    }
};

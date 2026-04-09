<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use App\Services\MikrotikService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DisableExpiredVouchers extends Command
{
    protected $signature = 'vouchers:disable-expired';

    protected $description = 'Disable hotspot users for expired vouchers and mark them expired';

    public function handle(MikrotikService $mikrotik): int
    {
        Log::info('DisableExpiredVouchers: started');

        $this->info('Disabling expired vouchers...');

        $expiredVouchers = Voucher::whereIn('status', ['new', 'used'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        Log::info('DisableExpiredVouchers: found expired vouchers', [
            'count' => $expiredVouchers->count(),
        ]);

        foreach ($expiredVouchers as $voucher) {
            try {
                Log::info('DisableExpiredVouchers: processing voucher', [
                    'voucher_id'   => $voucher->id,
                    'voucher_code' => $voucher->code,
                    'router_id'    => $voucher->router_id,
                    'username'     => $voucher->code,
                ]);

                if ($voucher->router && $voucher->code) {
                    $mikrotik->disableHotspotUser(
                        $voucher->router,
                        $voucher->code
                    );
                }

                // Mark as expired and stamp used_at if empty
                $voucher->status = 'expired';
                $voucher->used_at = $voucher->used_at ?? now();
                $voucher->save();

                Log::info('DisableExpiredVouchers: voucher expired and user disabled', [
                    'voucher_id' => $voucher->id,
                    'code'       => $voucher->code,
                ]);
            } catch (\Throwable $e) {
                Log::error('DisableExpiredVouchers: error processing voucher', [
                    'voucher_id' => $voucher->id,
                    'message'    => $e->getMessage(),
                    'trace'      => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info('Done.');
        Log::info('DisableExpiredVouchers: finished');

        // Update cron health heartbeat
        file_put_contents(
            storage_path('app/cron_vouchers_disable_last_ok'),
            now()->toDateTimeString()
        );

        return self::SUCCESS;
    }
}

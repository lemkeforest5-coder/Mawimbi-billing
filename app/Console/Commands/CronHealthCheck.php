<?php

namespace App\Console\Commands;

use App\Services\TelegramNotifier;
use Illuminate\Console\Command;

class CronHealthCheck extends Command
{
    protected $signature = 'system:cron-health';

    protected $description = 'Check health of cron and scheduled tasks (vouchers:disable-expired)';

    public function handle(TelegramNotifier $telegram): int
    {
        $this->info('Checking cron health...');

        $path = storage_path('app/cron_vouchers_disable_last_ok');

        if (! file_exists($path)) {
            $msg = 'Cron health ALERT: no heartbeat file found for vouchers:disable-expired.';
            $this->error($msg);
            $telegram->send($msg);

            return self::FAILURE;
        }

        $lastOk = trim(file_get_contents($path));
        $lastOkTime = \Carbon\Carbon::parse($lastOk);
        $diffMinutes = now()->diffInMinutes($lastOkTime);

        $this->line("Last vouchers:disable-expired success: {$lastOk} ({$diffMinutes} minutes ago)");

        // Consider unhealthy if last success was more than 90 minutes ago
        if (abs($diffMinutes) > 90) {
            $msg = sprintf(
                'Cron health ALERT on %s (%s): vouchers:disable-expired last ok at %s (%d minutes ago).',
                gethostname(),
                config('app.env'),
                $lastOk,
                $diffMinutes
            );

            $this->error($msg);
            $telegram->send($msg);

            return self::FAILURE;
        }

        $this->info('Cron health: OK');
        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\TelegramNotifier;
use Illuminate\Console\Command;

class MpesaHealthCheck extends Command
{
    protected $signature = 'system:mpesa-health';

    protected $description = 'Check health of Mpesa STK callbacks (last successful callback time)';

    public function handle(TelegramNotifier $telegram): int
    {
        $this->info('Checking Mpesa callback health...');

        $path = storage_path('app/mpesa_last_callback_ok');

        if (! file_exists($path)) {
            $msg = 'Mpesa health ALERT: no heartbeat file found for successful callbacks.';
            $this->error($msg);
            $telegram->send($msg);

            return self::FAILURE;
        }

        $lastOk = trim(file_get_contents($path));
        $lastOkTime = \Carbon\Carbon::parse($lastOk);
        $diffMinutes = now()->diffInMinutes($lastOkTime);

        $this->line("Last Mpesa callback success: {$lastOk} ({$diffMinutes} minutes ago)");

        // Unhealthy if last successful callback was more than 60 minutes ago
        if (abs($diffMinutes) > 60) {
            $msg = sprintf(
                'Mpesa health ALERT on %s (%s): last successful callback at %s (%d minutes ago).',
                gethostname(),
                config('app.env'),
                $lastOk,
                $diffMinutes
            );

            $this->error($msg);
            $telegram->send($msg);

            return self::FAILURE;
        }

        $this->info('Mpesa callback health: OK');
        return self::SUCCESS;
    }
}

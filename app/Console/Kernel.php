<?php

namespace App\Console;

use App\Console\Commands\CronHealthCheck;
use App\Console\Commands\MpesaHealthCheck;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        CronHealthCheck::class,
        MpesaHealthCheck::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // your existing schedule...

        // Cron job health
        $schedule->command('system:cron-health')->everyTenMinutes();

        // Mpesa callback health
        $schedule->command('system:mpesa-health')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Generate invoices for subscriptions due in 7 days
        $schedule->command('invoice:generate')
            ->dailyAt('00:01')
            ->withoutOverlapping();

        // Process pending reminders
        $schedule->command('reminder:process')
            ->hourly()
            ->withoutOverlapping();

        // Check and apply soft-limit
        $schedule->command('subscription:check-softlimit')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // Check and apply suspend
        $schedule->command('subscription:check-suspend')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // Sync routers
        $schedule->command('router:sync')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // Clean old audit logs (keep 90 days)
        $schedule->command('audit: cleanup --days=90')
            ->weekly()
            ->sundays()
            ->at('03:00');

        // Horizon snapshot
        $schedule->command('horizon:snapshot')
            ->everyFiveMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
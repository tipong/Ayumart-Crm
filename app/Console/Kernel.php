<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        /**
         * Cancel expired orders every minute
         * If payment is not completed within 15 minutes, order is automatically cancelled
         */
        $schedule->command('orders:cancel-expired')
            ->everyMinute()
            ->withoutOverlapping()
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('❌ CancelExpiredOrders command failed');
            })
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('✅ CancelExpiredOrders command completed successfully');
            });
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

<?php

namespace App\Console;

use App\Services\AutoResponseService;
use App\Services\IpBlockingService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cleanup expired IP blocks daily at 2:00 AM
        $schedule->call(function () {
            try {
                $ipBlockingService = app(IpBlockingService::class);
                $ipBlockingService->cleanupExpiredBlocks();
                \Log::info('Successfully cleaned up expired IP blocks');
            } catch (\Exception $e) {
                \Log::error('Error cleaning up expired IP blocks: '.$e->getMessage());
            }
        })->dailyAt('02:00');

        // Auto-unlock accounts every minute
        $schedule->call(function () {
            try {
                $autoResponseService = app(AutoResponseService::class);
                $autoResponseService->autoUnlockAccounts();
            } catch (\Exception $e) {
                \Log::error('Error auto-unlocking accounts: '.$e->getMessage());
            }
        })->everyMinute();

        // Cleanup expired auto-responses daily at 3:00 AM
        $schedule->call(function () {
            try {
                $autoResponseService = app(AutoResponseService::class);
                $autoResponseService->cleanupExpiredResponses();
                \Log::info('Successfully cleaned up expired auto-responses');
            } catch (\Exception $e) {
                \Log::error('Error cleaning up expired auto-responses: '.$e->getMessage());
            }
        })->dailyAt('03:00');
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

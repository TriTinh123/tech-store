<?php

use App\Services\AutoResponseService;
use App\Services\IpBlockingService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'require_3fa' => \App\Http\Middleware\Require3FA::class,
            'track_session' => \App\Http\Middleware\SessionTrackingMiddleware::class,
        ]);

        // Add session tracking middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\SessionTrackingMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
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

        // Cleanup old sessions daily at 4:00 AM
        $schedule->call(function () {
            try {
                $sessionMonitoringService = app(\App\Services\SessionMonitoringService::class);
                $sessionMonitoringService->cleanupOldSessions(30);
                \Log::info('Successfully cleaned up old sessions');
            } catch (\Exception $e) {
                \Log::error('Error cleaning up old sessions: '.$e->getMessage());
            }
        })->dailyAt('04:00');

        // Cleanup old activities daily at 4:00 AM
        $schedule->call(function () {
            try {
                $sessionMonitoringService = app(\App\Services\SessionMonitoringService::class);
                $sessionMonitoringService->cleanupOldActivities(30);
                \Log::info('Successfully cleaned up old session activities');
            } catch (\Exception $e) {
                \Log::error('Error cleaning up old session activities: '.$e->getMessage());
            }
        })->dailyAt('04:00');

        // Cleanup old notifications daily at 5:00 AM
        $schedule->call(function () {
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->cleanupOldNotifications(90);
                $notificationService->cleanupOldLogs(30);
                \Log::info('Successfully cleaned up old notifications and logs');
            } catch (\Exception $e) {
                \Log::error('Error cleaning up notifications: '.$e->getMessage());
            }
        })->dailyAt('05:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

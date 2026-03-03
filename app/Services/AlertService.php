<?php

namespace App\Services;

use App\Mail\SecurityAlertMail;
use App\Models\SecurityAlert;
use App\Models\SuspiciousLogin;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AlertService
{
    /**
     * Dispatch alerts to user through selected channels
     */
    public function dispatchAlert(
        User $user,
        string $alertType,
        string $message,
        string $severity = 'medium',
        ?SuspiciousLogin $suspiciousLogin = null,
        array $channels = ['email']
    ): SecurityAlert {
        // Create alert record
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'suspicious_login_id' => $suspiciousLogin?->id,
            'alert_type' => $alertType,
            'message' => $message,
            'severity' => $severity,
            'notification_channels' => $channels,
        ]);

        // Send through selected channels
        foreach ($channels as $channel) {
            match ($channel) {
                'email' => $this->sendEmailAlert($alert, $user),
                'website' => $this->addWebsiteNotification($alert, $user),
                'sms' => $this->sendSmsAlert($alert, $user),
                default => null,
            };
        }

        return $alert;
    }

    /**
     * Send email alert to user
     */
    public function sendEmailAlert(SecurityAlert $alert, User $user): void
    {
        try {
            Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));
            $alert->markAsSent();
        } catch (\Exception $e) {
            \Log::error('Failed to send security alert email', [
                'user_id' => $user->id,
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Add website notification (in-app notification)
     * Stores in notifications table for displaying in dashboard
     */
    public function addWebsiteNotification(SecurityAlert $alert, User $user): void
    {
        // Create notification record in database for in-app display
        // This would typically use Laravel's Notification system
        // For now, alert record itself serves as notification
        $alert->markAsSent();
    }

    /**
     * Send SMS alert (optional - requires SMS provider like Twilio)
     */
    public function sendSmsAlert(SecurityAlert $alert, User $user): void
    {
        // TODO: Implement SMS integration with Twilio or similar service
        // Example:
        // $twilio = new Twilio\Rest\Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        // $twilio->messages->create($user->phone, [
        //     'from' => env('TWILIO_PHONE_NUMBER'),
        //     'body' => $alert->message
        // ]);

        \Log::info('SMS alert feature not yet implemented', [
            'user_id' => $user->id,
            'alert_id' => $alert->id,
        ]);
    }

    /**
     * Build alert message based on alert type and context
     */
    public function buildAlertMessage(
        string $alertType,
        array $context = []
    ): string {
        return match ($alertType) {
            'new_ip' => $this->buildNewIpMessage($context),
            'new_device' => $this->buildNewDeviceMessage($context),
            'unusual_time' => $this->buildUnusualTimeMessage($context),
            'rapid_location' => $this->buildRapidLocationMessage($context),
            'failed_attempt' => $this->buildFailedAttemptMessage($context),
            'account_locked' => $this->buildAccountLockedMessage($context),
            default => 'Phát hiện hoạt động bất thường trên tài khoản của bạn',
        };
    }

    /**
     * Build message for new IP alert
     */
    private function buildNewIpMessage(array $context): string
    {
        $ipAddress = $context['ip_address'] ?? 'Unknown';
        $location = $context['location'] ?? 'Unknown location';
        $time = $context['time'] ?? now()->format('H:i:s d/m/Y');

        return "⚠️ Phát hiện đăng nhập từ IP mới ({$ipAddress}) từ vị trí {$location} vào lúc {$time}.";
    }

    /**
     * Build message for new device alert
     */
    private function buildNewDeviceMessage(array $context): string
    {
        $device = $context['device'] ?? 'Unknown device';
        $browser = $context['browser'] ?? 'Unknown browser';
        $time = $context['time'] ?? now()->format('H:i:s d/m/Y');

        return "⚠️ Phát hiện đăng nhập từ thiết bị mới ({$device} - {$browser}) vào lúc {$time}.";
    }

    /**
     * Build message for unusual time alert
     */
    private function buildUnusualTimeMessage(array $context): string
    {
        $time = $context['time'] ?? now()->format('H:i:s d/m/Y');
        $ipAddress = $context['ip_address'] ?? 'Unknown';

        return "⚠️ Phát hiện đăng nhập vào thời gian bất thường ({$time}) từ IP {$ipAddress}.";
    }

    /**
     * Build message for rapid location change alert
     */
    private function buildRapidLocationMessage(array $context): string
    {
        $previousLocation = $context['previous_location'] ?? 'Unknown';
        $currentLocation = $context['current_location'] ?? 'Unknown';
        $distance = $context['distance'] ?? 0;
        $time = $context['time'] ?? now()->format('H:i:s d/m/Y');

        return "⚠️ Phát hiện đăng nhập từ vị trí quá xa (Từ {$previousLocation} đến {$currentLocation} - {$distance}km) vào lúc {$time}.";
    }

    /**
     * Build message for failed login attempts alert
     */
    private function buildFailedAttemptMessage(array $context): string
    {
        $attempts = $context['attempts'] ?? 0;
        $time = $context['time'] ?? now()->format('H:i:s d/m/Y');

        return "⚠️ Phát hiện {$attempts} lần đăng nhập thất bại vào lúc {$time}. Kiểm tra nếu đây không phải là bạn.";
    }

    /**
     * Build message for account locked alert
     */
    private function buildAccountLockedMessage(array $context): string
    {
        $attempts = $context['attempts'] ?? 5;
        $duration = $context['duration'] ?? 15;

        return "🔒 Tài khoản của bạn đã bị khóa tạm thời vì có {$attempts} lần đăng nhập thất bại liên tiếp. Vui lòng thử lại sau {$duration} phút.";
    }

    /**
     * Get user notification preferences (if implemented)
     * For now, return default channels
     */
    public function getUserNotificationChannels(User $user): array
    {
        // TODO: Fetch from user.notification_preferences column or separate table
        // For now, default to email
        return ['email'];
    }

    /**
     * Cleanup old alerts (keep last 3 months)
     */
    public function cleanupOldAlerts(?int $daysToKeep = 90): int
    {
        return SecurityAlert::where('created_at', '<', now()->subDays($daysToKeep)->startOfDay())
            ->delete();
    }

    /**
     * Get alert statistics for user dashboard
     */
    public function getUserAlertStats(User $user): array
    {
        return [
            'unread_count' => $user->securityAlerts()->unread()->count(),
            'critical_count' => $user->securityAlerts()->bySeverity('critical')->unread()->count(),
            'recent_alerts' => $user->securityAlerts()
                ->recent(1440) // Last 24 hours
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];
    }
}

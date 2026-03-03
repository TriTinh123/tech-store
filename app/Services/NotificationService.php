<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class NotificationService
{
    /**
     * Send security notification to user across configured channels
     */
    public function sendSecurityNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        string $severity = 'warning',
        ?array $details = null,
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): Notification {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'details' => $details,
            'severity' => $severity,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
        ]);

        // Send via configured channels
        $this->sendViaConfiguredChannels($user, $notification);

        return $notification;
    }

    /**
     * Send notification via all configured channels
     */
    private function sendViaConfiguredChannels(User $user, Notification $notification): void
    {
        $preferences = $user->notificationPreferences ?? NotificationPreference::firstOrCreate([
            'user_id' => $user->id,
        ]);

        // Check if user wants notification for this event type
        if (! $preferences->shouldNotifyForEvent($notification->type)) {
            return;
        }

        $channels = $preferences->getEnabledChannels();

        foreach ($channels as $channel) {
            $this->sendViaChannel($user, $notification, $channel);
        }
    }

    /**
     * Send notification via specific channel
     */
    public function sendViaChannel(User $user, Notification $notification, string $channel): void
    {
        match ($channel) {
            'email' => $this->sendEmailNotification($user, $notification),
            'sms' => $this->sendSmsNotification($user, $notification),
            'in_app' => $this->createInAppNotification($user, $notification),
            default => null,
        };
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, Notification $notification): void
    {
        try {
            $preferences = $user->notificationPreferences;
            $email = $preferences?->notification_email ?? $user->email;

            $log = NotificationLog::create([
                'user_id' => $user->id,
                'channel' => 'email',
                'event_type' => $notification->type,
                'recipient' => $email,
                'status' => 'pending',
                'content' => $notification->message,
            ]);

            // Queue email job
            Queue::push(function ($job) use ($user, $notification, $email, $log) {
                try {
                    Mail::send('emails.security-notification', [
                        'user' => $user,
                        'notification' => $notification,
                    ], function ($message) use ($email, $notification) {
                        $message->to($email)
                            ->subject("[{$notification->severity}] {$notification->title}");
                    });

                    $log->markAsSent();
                    $job->delete();
                } catch (\Exception $e) {
                    Log::error('Email notification failed', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                    $log->markAsFailed($e->getMessage());

                    if ($log->canRetry()) {
                        $job->release(300); // Retry in 5 minutes
                    } else {
                        $job->delete();
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to queue email notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send SMS notification (Twilio/AWS SNS)
     */
    private function sendSmsNotification(User $user, Notification $notification): void
    {
        try {
            $preferences = $user->notificationPreferences;
            $phoneNumber = $preferences?->phone_number;

            if (! $phoneNumber) {
                return;
            }

            $log = NotificationLog::create([
                'user_id' => $user->id,
                'channel' => 'sms',
                'event_type' => $notification->type,
                'recipient' => $phoneNumber,
                'status' => 'pending',
                'content' => $this->formatSmsMessage($notification),
            ]);

            // Queue SMS job
            Queue::push(function ($job) use ($user, $notification, $phoneNumber, $log) {
                try {
                    // Use configured SMS provider (Twilio, AWS SNS, etc.)
                    $this->sendViaSmsProvider($phoneNumber, $notification, $log);
                    $job->delete();
                } catch (\Exception $e) {
                    Log::error('SMS notification failed', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                    $log->markAsFailed($e->getMessage());

                    if ($log->canRetry()) {
                        $job->release(300);
                    } else {
                        $job->delete();
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to queue SMS notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send via SMS provider (Twilio, AWS SNS, etc.)
     */
    private function sendViaSmsProvider(string $phoneNumber, Notification $notification, NotificationLog $log): void
    {
        $provider = config('services.sms_provider', 'twilio'); // Default to Twilio

        match ($provider) {
            'twilio' => $this->sendViaTwilio($phoneNumber, $notification, $log),
            'aws_sns' => $this->sendViaAwsSns($phoneNumber, $notification, $log),
            'vonage' => $this->sendViaVonage($phoneNumber, $notification, $log),
            default => throw new \Exception("Unknown SMS provider: {$provider}"),
        };
    }

    /**
     * Send via Twilio
     */
    private function sendViaTwilio(string $phoneNumber, Notification $notification, NotificationLog $log): void
    {
        try {
            if (! class_exists('\Twilio\Rest\Client')) {
                throw new \Exception('Twilio SDK not installed. Run: composer require twilio/sdk');
            }

            $message = $this->formatSmsMessage($notification);
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $fromNumber = config('services.twilio.from_number');

            if (! $accountSid || ! $authToken || ! $fromNumber) {
                throw new \Exception('Twilio credentials not configured');
            }

            $className = '\\Twilio\\Rest\\Client';
            /** @phpstan-ignore-next-line */
            $client = new $className($accountSid, $authToken);
            $response = $client->messages->create(
                $phoneNumber,
                ['from' => $fromNumber, 'body' => $message]
            );

            $log->markAsSent($response->sid, [
                'provider' => 'twilio',
                'status' => $response->status,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Send via AWS SNS
     */
    private function sendViaAwsSns(string $phoneNumber, Notification $notification, NotificationLog $log): void
    {
        try {
            if (! class_exists('\\Aws\\Sns\\SnsClient')) {
                throw new \Exception('AWS SDK not installed. Run: composer require aws/aws-sdk-php');
            }

            $className = '\\Aws\\Sns\\SnsClient';
            /** @phpstan-ignore-next-line */
            $client = new $className([
                'version' => 'latest',
                'region' => config('services.aws.region'),
                'credentials' => [
                    'key' => config('services.aws.key'),
                    'secret' => config('services.aws.secret'),
                ],
            ]);

            $message = $this->formatSmsMessage($notification);

            $response = $client->publish([
                'Message' => $message,
                'PhoneNumber' => $phoneNumber,
            ]);

            $log->markAsSent($response['MessageId'], [
                'provider' => 'aws_sns',
                'status' => 'sent',
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Send via Vonage (Nexmo)
     */
    private function sendViaVonage(string $phoneNumber, Notification $notification, NotificationLog $log): void
    {
        try {
            if (! class_exists('\Vonage\Client')) {
                throw new \Exception('Vonage SDK not installed. Run: composer require vonage/client-core');
            }

            $className = '\\Vonage\\Client';
            $credClassName = '\\Vonage\\Client\\Credentials\\Basic';
            /** @phpstan-ignore-next-line */
            $client = new $className(
                /** @phpstan-ignore-next-line */
                new $credClassName(
                    config('services.vonage.api_key'),
                    config('services.vonage.api_secret')
                )
            );

            $message = $this->formatSmsMessage($notification);

            $smsClassName = '\\Vonage\\SMS\\Message\\SMS';
            $response = $client->sms()->sendMessage(
                /** @phpstan-ignore-next-line */
                new $smsClassName(
                    $phoneNumber,
                    config('services.vonage.from'),
                    $message
                )
            );

            $log->markAsSent($response->getMessageId(), [
                'provider' => 'vonage',
                'status' => 'sent',
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create in-app notification
     */
    private function createInAppNotification(User $user, Notification $notification): void
    {
        // Already created in database
        // In real-time apps, broadcast via WebSocket
        $this->broadcastNotification($user, $notification);
    }

    /**
     * Broadcast notification via WebSocket (Pusher, Laravel Websockets, etc.)
     */
    private function broadcastNotification(User $user, Notification $notification): void
    {
        try {
            // Broadcast to user's notification channel
            broadcast(new \App\Events\NotificationReceived($user, $notification));
        } catch (\Exception $e) {
            Log::debug('Broadcast failed (likely local environment)', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format SMS message (keep it short)
     */
    private function formatSmsMessage(Notification $notification): string
    {
        $message = "[{$notification->severity}] {$notification->title}: {$notification->message}";

        // Keep under 160 characters for single SMS
        if (strlen($message) > 160) {
            $message = substr($message, 0, 157).'...';
        }

        return $message;
    }

    /**
     * Send concurrent login alert
     */
    public function sendConcurrentLoginAlert(User $user, array $locations): void
    {
        $locationStr = implode(' → ', $locations);

        $this->sendSecurityNotification(
            $user,
            'concurrent_login',
            '🚨 Đăng nhập đồng thời phát hiện',
            "Phát hiện đăng nhập từ hai vị trí khác nhau: {$locationStr}",
            'critical',
            ['locations' => $locations],
            route('profile.sessions.index'),
            'Xem các phiên của bạn'
        );
    }

    /**
     * Send suspicious activity alert
     */
    public function sendSuspiciousActivityAlert(User $user, string $reason): void
    {
        $this->sendSecurityNotification(
            $user,
            'suspicious_activity',
            '⚠️ Hoạt động đáng ngờ phát hiện',
            "Hoạt động bất thường trên tài khoản: {$reason}",
            'warning',
            null,
            route('profile.alerts.index'),
            'Xem chi tiết'
        );
    }

    /**
     * Send IP blocked alert
     */
    public function sendIpBlockedAlert(User $user, string $ipAddress, string $reason = ''): void
    {
        $this->sendSecurityNotification(
            $user,
            'ip_blocked',
            '🚫 Địa chỉ IP bị chặn',
            "Địa chỉ IP {$ipAddress} đã bị chặn. ".($reason ?: ''),
            'critical',
            ['ip_address' => $ipAddress, 'reason' => $reason],
            route('profile.alerts.index'),
            'Xem chi tiết'
        );
    }

    /**
     * Send 3FA setup confirmation
     */
    public function send3faSetupConfirmation(User $user, string $method): void
    {
        $this->sendSecurityNotification(
            $user,
            '3fa_changes',
            '✓ 3FA Setup thành công',
            "Xác thực ba yếu tố ({$method}) đã được kích hoạt",
            'info',
            ['method' => $method],
        );
    }

    /**
     * Send new device login alert
     */
    public function sendNewDeviceAlert(User $user, string $deviceInfo, string $location): void
    {
        $this->sendSecurityNotification(
            $user,
            'new_device',
            '📱 Thiết bị mới phát hiện',
            "Đăng nhập từ thiết bị mới: {$deviceInfo} tại {$location}",
            'info',
            ['device' => $deviceInfo, 'location' => $location],
            route('profile.sessions.index'),
            'Xem các phiên'
        );
    }

    /**
     * Send location change alert
     */
    public function sendLocationChangeAlert(User $user, string $oldLocation, string $newLocation): void
    {
        $this->sendSecurityNotification(
            $user,
            'location_change',
            '📍 Thay đổi vị trí phát hiện',
            "Đăng nhập từ vị trí mới: {$oldLocation} → {$newLocation}",
            'warning',
            ['old_location' => $oldLocation, 'new_location' => $newLocation],
            route('profile.sessions.index'),
            'Xem chi tiết'
        );
    }

    /**
     * Get notification statistics for user
     */
    public function getStatistics(User $user): array
    {
        return [
            'unread_count' => Notification::where('user_id', $user->id)
                ->unread()
                ->count(),
            'critical_count' => Notification::where('user_id', $user->id)
                ->bySeverity('critical')
                ->count(),
            'total_notifications' => Notification::where('user_id', $user->id)->count(),
            'email_sent_today' => NotificationLog::where('user_id', $user->id)
                ->byChannel('email')
                ->whereDate('sent_at', now())
                ->count(),
            'failed_notifications' => NotificationLog::where('user_id', $user->id)
                ->failed()
                ->count(),
        ];
    }

    /**
     * Clean up old notifications
     */
    public function cleanupOldNotifications(int $daysRetention = 90): int
    {
        return Notification::where('created_at', '<', now()->subDays($daysRetention))
            ->delete();
    }

    /**
     * Clean up old notification logs
     */
    public function cleanupOldLogs(int $daysRetention = 30): int
    {
        return NotificationLog::where('created_at', '<', now()->subDays($daysRetention))
            ->delete();
    }
}

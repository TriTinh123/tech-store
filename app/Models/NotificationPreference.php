<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_enabled',
        'sms_enabled',
        'in_app_enabled',
        'notify_concurrent_login',
        'notify_suspicious_activity',
        'notify_3fa_changes',
        'notify_ip_blocked',
        'notify_password_change',
        'notify_new_device',
        'notify_location_change',
        'phone_number',
        'phone_verified',
        'phone_verified_at',
        'notification_email',
        'email_verified',
        'email_frequency',
        'sms_frequency',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'notify_concurrent_login' => 'boolean',
        'notify_suspicious_activity' => 'boolean',
        'notify_3fa_changes' => 'boolean',
        'notify_ip_blocked' => 'boolean',
        'notify_password_change' => 'boolean',
        'notify_new_device' => 'boolean',
        'notify_location_change' => 'boolean',
        'phone_verified' => 'boolean',
        'email_verified' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user wants notification for event type
     */
    public function shouldNotifyForEvent(string $eventType): bool
    {
        // Check if during quiet hours
        if ($this->isInQuietHours()) {
            return false;
        }

        return match ($eventType) {
            'concurrent_login' => $this->notify_concurrent_login,
            'suspicious_activity' => $this->notify_suspicious_activity,
            '3fa_changes' => $this->notify_3fa_changes,
            'ip_blocked' => $this->notify_ip_blocked,
            'password_change' => $this->notify_password_change,
            'new_device' => $this->notify_new_device,
            'location_change' => $this->notify_location_change,
            default => true,
        };
    }

    /**
     * Check if currently in quiet hours
     */
    public function isInQuietHours(): bool
    {
        if (! $this->quiet_hours_enabled) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->quiet_hours_start?->format('H:i');
        $end = $this->quiet_hours_end?->format('H:i');

        if (! $start || ! $end) {
            return false;
        }

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        } else {
            // Quiet hours cross midnight
            return $now >= $start || $now <= $end;
        }
    }

    /**
     * Get enabled notification channels
     */
    public function getEnabledChannels(): array
    {
        $channels = [];
        if ($this->email_enabled && $this->email_verified) {
            $channels[] = 'email';
        }
        if ($this->sms_enabled && $this->phone_verified) {
            $channels[] = 'sms';
        }
        if ($this->in_app_enabled) {
            $channels[] = 'in_app';
        }

        return $channels;
    }

    /**
     * Enable email notifications
     */
    public function enableEmail(string $email): self
    {
        $this->update([
            'notification_email' => $email,
            'email_enabled' => true,
            'email_verified' => true,
        ]);

        return $this;
    }

    /**
     * Enable SMS notifications
     */
    public function enableSms(string $phoneNumber): self
    {
        $this->update([
            'phone_number' => $phoneNumber,
            'sms_enabled' => true,
            'phone_verified' => true,
            'phone_verified_at' => now(),
        ]);

        return $this;
    }

    /**
     * Set quiet hours
     */
    public function setQuietHours(string $start, string $end, bool $enabled = true): self
    {
        $this->update([
            'quiet_hours_start' => $start,
            'quiet_hours_end' => $end,
            'quiet_hours_enabled' => $enabled,
        ]);

        return $this;
    }
}

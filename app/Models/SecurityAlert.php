<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAlert extends Model
{
    protected $fillable = [
        'user_id',
        'suspicious_login_id',
        'alert_type',
        'message',
        'severity',
        'notification_channels',
        'sent_at',
        'read_at',
        'confirmed_by_user',
    ];

    protected $casts = [
        'notification_channels' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'confirmed_by_user' => 'boolean',
    ];

    /**
     * Get the user that this alert belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the suspicious login that triggered this alert
     */
    public function suspiciousLogin(): BelongsTo
    {
        return $this->belongsTo(SuspiciousLogin::class);
    }

    /**
     * Mark alert as sent
     */
    public function markAsSent(): self
    {
        $this->update(['sent_at' => now()]);

        return $this;
    }

    /**
     * Mark alert as read (for website notifications)
     */
    public function markAsRead(): self
    {
        $this->update(['read_at' => now()]);

        return $this;
    }

    /**
     * Mark alert as confirmed by user
     */
    public function confirmByUser(): self
    {
        $this->update(['confirmed_by_user' => true]);

        return $this;
    }

    /**
     * Check if alert was sent
     */
    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    /**
     * Check if alert was read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Get human-readable alert type label
     */
    public function getAlertTypeLabel(): string
    {
        return match ($this->alert_type) {
            'new_ip' => 'Đăng nhập từ IP mới',
            'new_device' => 'Đăng nhập từ thiết bị lạ',
            'unusual_time' => 'Đăng nhập vào thời gian bất thường',
            'rapid_location' => 'Đăng nhập từ vị trí quá xa',
            'failed_attempt' => 'Có nhiều lần đăng nhập thất bại',
            'account_locked' => 'Tài khoản đã bị khóa',
            default => ucfirst(str_replace('_', ' ', $this->alert_type)),
        };
    }

    /**
     * Get human-readable severity label with color
     */
    public function getSeverityBadge(): array
    {
        return match ($this->severity) {
            'low' => ['label' => 'Thấp', 'color' => 'blue'],
            'medium' => ['label' => 'Trung bình', 'color' => 'yellow'],
            'high' => ['label' => 'Cao', 'color' => 'orange'],
            'critical' => ['label' => 'Nghiêm trọng', 'color' => 'red'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }

    /**
     * Scope: Get unread alerts for a user
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope: Get alerts by severity
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope: Get unsent alerts
     */
    public function scopeUnsent($query)
    {
        return $query->whereNull('sent_at');
    }

    /**
     * Scope: Get recent alerts
     */
    public function scopeRecent($query, ?int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
}

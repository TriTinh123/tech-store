<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    use HasFactory;

    protected $table = 'blocked_ips';

    protected $fillable = [
        'ip_address',
        'country_code',
        'location',
        'block_type',
        'reason',
        'is_permanent',
        'blocked_at',
        'unblock_at',
        'blocked_by_admin_id',
        'failed_attempts',
        'suspicious_activities_count',
        'total_login_attempts',
        'last_attempt_at',
        'risk_level',
        'suspicious_patterns',
        'requires_email_verification',
        'requires_otp_unlock',
        'unlock_conditions',
        'notes',
        'history',
    ];

    protected $casts = [
        'is_permanent' => 'boolean',
        'blocked_at' => 'datetime',
        'unblock_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'requires_email_verification' => 'boolean',
        'requires_otp_unlock' => 'boolean',
        'suspicious_patterns' => 'json',
        'unlock_conditions' => 'json',
        'history' => 'json',
    ];

    /**
     * Get the admin who blocked this IP
     */
    public function blockedByAdmin()
    {
        return $this->belongsTo(User::class, 'blocked_by_admin_id');
    }

    /**
     * Scope: Get all currently active blocks
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_permanent', true)
                ->orWhere('unblock_at', '>', now());
        });
    }

    /**
     * Scope: Get permanent blocks
     */
    public function scopePermanent($query)
    {
        return $query->where('is_permanent', true);
    }

    /**
     * Scope: Get temporary blocks
     */
    public function scopeTemporary($query)
    {
        return $query->where('is_permanent', false);
    }

    /**
     * Scope: Get expired blocks
     */
    public function scopeExpired($query)
    {
        return $query->where('is_permanent', false)
            ->where('unblock_at', '<=', now());
    }

    /**
     * Scope: Get auto blocks (from attacks or failed attempts)
     */
    public function scopeAutoBlocked($query)
    {
        return $query->whereIn('block_type', ['auto_attack', 'auto_failed_attempts']);
    }

    /**
     * Scope: Get manual blocks
     */
    public function scopeManual($query)
    {
        return $query->where('block_type', 'manual');
    }

    /**
     * Scope: Get critical risk IPs
     */
    public function scopeCritical($query)
    {
        return $query->where('risk_level', 'critical');
    }

    /**
     * Check if this IP is currently blocked
     */
    public function isActive()
    {
        if ($this->is_permanent) {
            return true;
        }

        return $this->unblock_at && $this->unblock_at->isFuture();
    }

    /**
     * Check if block has expired
     */
    public function isExpired()
    {
        if ($this->is_permanent) {
            return false;
        }

        return $this->unblock_at && $this->unblock_at->isPast();
    }

    /**
     * Get block type label
     */
    public function getBlockTypeLabel()
    {
        return match ($this->block_type) {
            'manual' => 'Thủ công (Manual)',
            'auto_attack' => 'Tự động - Tấn công (Auto - Attack)',
            'auto_failed_attempts' => 'Tự động - Đăng nhập thất bại (Auto - Failed Attempts)',
            default => $this->block_type,
        };
    }

    /**
     * Get risk level label
     */
    public function getRiskLevelLabel()
    {
        return match ($this->risk_level) {
            'low' => 'Thấp (Low)',
            'medium' => 'Trung bình (Medium)',
            'high' => 'Cao (High)',
            'critical' => 'Nguy cấp (Critical)',
            default => $this->risk_level,
        };
    }

    /**
     * Add record to block history
     */
    public function addToHistory($action, $details = [])
    {
        $history = $this->history ?? [];
        $history[] = [
            'action' => $action,
            'timestamp' => now()->toIso8601String(),
            'details' => $details,
        ];
        $this->history = $history;

        return $this;
    }

    /**
     * Unblock this IP
     */
    public function unblock()
    {
        $this->addToHistory('unblocked', ['unblocked_at' => now()]);
        $this->unblock_at = null;
        $this->save();
    }

    /**
     * Update failed attempt count
     */
    public function recordFailedAttempt()
    {
        $this->failed_attempts++;
        $this->last_attempt_at = now();
        $this->total_login_attempts++;
        $this->save();
    }

    /**
     * Increment suspicious activity counter
     */
    public function recordSuspiciousActivity($activity)
    {
        $this->suspicious_activities_count++;
        $this->addToHistory('suspicious_activity', ['activity' => $activity]);
        $this->save();
    }

    /**
     * Get remaining block time (for temporary blocks)
     */
    public function getRemainingBlockTime()
    {
        if ($this->is_permanent) {
            return 'Vĩnh viễn (Permanent)';
        }
        if (! $this->unblock_at) {
            return 'Không xác định (Indefinite)';
        }
        $diff = $this->unblock_at->diffInMinutes(now());
        if ($diff <= 0) {
            return 'Hết hạn (Expired)';
        }
        if ($diff <= 60) {
            return "{$diff} phút";
        }
        $hours = intdiv($diff, 60);

        return "{$hours} giờ";
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSession extends Model
{
    protected $table = 'user_sessions';

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_name',
        'device_type',
        'browser',
        'os',
        'location',
        'latitude',
        'longitude',
        'last_activity_at',
        'last_activity_ip',
        'last_activity_url',
        'logged_out_at',
        'is_active',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'is_active' => 'boolean',
        'is_flagged' => 'boolean',
    ];

    /**
     * Get the user that owns this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get activities for this session
     */
    public function activities(): HasMany
    {
        return $this->hasMany(SessionActivity::class);
    }

    /**
     * Check if session is currently active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->logged_out_at === null;
    }

    /**
     * Get device information
     */
    public function getDeviceInfo(): string
    {
        if ($this->device_name) {
            return $this->device_name;
        }

        $parts = [];
        if ($this->browser) {
            $parts[] = $this->browser;
        }
        if ($this->os) {
            $parts[] = $this->os;
        }

        return implode(' - ', $parts) ?: 'Unknown Device';
    }

    /**
     * Get time since last activity
     */
    public function getTimeSinceLastActivity(): string
    {
        if (! $this->last_activity_at) {
            return 'No activity';
        }

        $diff = $this->last_activity_at->diffInSeconds(now());

        if ($diff < 60) {
            return "$diff seconds ago";
        } elseif ($diff < 3600) {
            $minutes = intval($diff / 60);

            return "$minutes minutes ago";
        } elseif ($diff < 86400) {
            $hours = intval($diff / 3600);

            return "$hours hours ago";
        } else {
            $days = intval($diff / 86400);

            return "$days days ago";
        }
    }

    /**
     * Mark session as logged out
     */
    public function logout(): self
    {
        $this->update([
            'is_active' => false,
            'logged_out_at' => now(),
        ]);

        return $this;
    }

    /**
     * Touch activity timestamp
     */
    public function touchActivity(): self
    {
        $this->update(['last_activity_at' => now()]);

        return $this;
    }

    /**
     * Flag session as suspicious
     */
    public function flag(string $reason): self
    {
        $this->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
        ]);

        return $this;
    }

    /**
     * Unflag session
     */
    public function unflag(): self
    {
        $this->update([
            'is_flagged' => false,
            'flag_reason' => null,
        ]);

        return $this;
    }

    /**
     * Scope: Get active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereNull('logged_out_at');
    }

    /**
     * Scope: Get flagged sessions
     */
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    /**
     * Scope: Get sessions for a user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get sessions with recent activity
     */
    public function scopeRecentActivity($query, int $minutes = 30)
    {
        return $query->where('last_activity_at', '>=', now()->subMinutes($minutes));
    }
}

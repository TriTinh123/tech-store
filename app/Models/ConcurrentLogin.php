<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcurrentLogin extends Model
{
    protected $table = 'concurrent_logins';

    protected $fillable = [
        'user_id',
        'primary_session_id',
        'secondary_session_id',
        'primary_ip_address',
        'secondary_ip_address',
        'primary_location',
        'secondary_location',
        'time_difference_seconds',
        'status',
        'admin_notes',
        'confirmed_at',
        'confirmed_by',
        'resolved_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user involved in concurrent login
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that confirmed the concurrent login
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Check if concurrent login is from same location
     */
    public function isSameLocation(): bool
    {
        return $this->primary_location === $this->secondary_location;
    }

    /**
     * Check if time difference is suspicious
     */
    public function isSuspiciousTimeDifference(int $minSeconds = 5): bool
    {
        return ($this->time_difference_seconds ?? 999) < $minSeconds;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $statuses = [
            'detected' => 'Phát hiện',
            'confirmed' => 'Đã xác nhận',
            'authorized' => 'Được phép',
            'false_positive' => 'Dương tính giả',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Confirm concurrent login as suspicious
     */
    public function confirm(int $adminId, string $notes = ''): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => $adminId,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Mark as authorized/legitimate
     */
    public function authorize(int $adminId): void
    {
        $this->update([
            'status' => 'authorized',
            'confirmed_at' => now(),
            'confirmed_by' => $adminId,
        ]);
    }

    /**
     * Mark as false positive
     */
    public function markFalsePositive(int $adminId, string $reason = ''): void
    {
        $this->update([
            'status' => 'false_positive',
            'confirmed_at' => now(),
            'confirmed_by' => $adminId,
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Scope for unresolved logins
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at')
            ->where('status', '!=', 'authorized')
            ->where('status', '!=', 'false_positive');
    }

    /**
     * Scope for confirmed logins
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

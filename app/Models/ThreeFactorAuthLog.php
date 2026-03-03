<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeFactorAuthLog extends Model
{
    protected $table = 'three_factor_auth_logs';

    protected $fillable = [
        'user_id',
        'auth_method',
        'status',
        'ip_address',
        'user_agent',
        'device_info',
        'failure_reason',
        'attempt_number',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that made the authentication attempt
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record successful authentication
     */
    public function markAsSuccess(): void
    {
        $this->update([
            'status' => 'success',
            'verified_at' => now(),
        ]);
    }

    /**
     * Record failed authentication
     */
    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Get authentication status
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if authentication is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if authentication failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get human readable auth method
     */
    public function getAuthMethodLabel(): string
    {
        $methods = [
            'otp' => 'OTP (Email/SMS)',
            'security_question' => 'Câu hỏi bảo mật',
            'biometric' => 'Sinh trắc học',
        ];

        return $methods[$this->auth_method] ?? $this->auth_method;
    }

    /**
     * Get human readable status
     */
    public function getStatusLabel(): string
    {
        $statuses = [
            'pending' => 'Chờ xác thực',
            'success' => 'Thành công',
            'failed' => 'Thất bại',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Scope for successful attempts
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific auth method
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('auth_method', $method);
    }

    /**
     * Scope for recent attempts (last 24 hours)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDay());
    }
}

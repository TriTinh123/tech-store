<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpSetting extends Model
{
    protected $fillable = [
        'user_id',
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
        'is_email_verified',
        'is_sms_verified',
        'phone_number',
        'otp_delivery_method',
        'otp_enabled',
        'last_otp_sent_at',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'last_otp_sent_at' => 'datetime',
        'is_email_verified' => 'boolean',
        'is_sms_verified' => 'boolean',
        'otp_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the OTP setting
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP has expired
     */
    public function isOtpExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    /**
     * Check if OTP attempts exceeded limit
     */
    public function hasExceededAttempts(int $limit = 5): bool
    {
        return $this->otp_attempts >= $limit;
    }

    /**
     * Reset OTP settings
     */
    public function resetOtp(): void
    {
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ]);
    }

    /**
     * Increment OTP attempts
     */
    public function incrementOtpAttempts(): void
    {
        $this->increment('otp_attempts');
    }

    /**
     * Set OTP delivery method
     */
    public function setDeliveryMethod(string $method): void
    {
        $this->update(['otp_delivery_method' => $method]);
    }

    /**
     * Verify email method
     */
    public function verifyEmailMethod(): void
    {
        $this->update(['is_email_verified' => true]);
    }

    /**
     * Verify SMS method
     */
    public function verifySmsMethod(): void
    {
        $this->update(['is_sms_verified' => true]);
    }

    /**
     * Check if both email and SMS are verified
     */
    public function isBothMethodsVerified(): bool
    {
        return $this->is_email_verified && $this->is_sms_verified;
    }

    /**
     * Get delivery methods that are verified
     */
    public function getVerifiedMethods(): array
    {
        $methods = [];
        if ($this->is_email_verified) {
            $methods[] = 'email';
        }
        if ($this->is_sms_verified) {
            $methods[] = 'sms';
        }

        return $methods;
    }

    /**
     * Check if can send OTP (rate limiting)
     */
    public function canSendOtp(int $intervalSeconds = 10): bool
    {
        if (! $this->last_otp_sent_at) {
            return true;
        }

        return $this->last_otp_sent_at->addSeconds($intervalSeconds)->isPast();
    }

    /**
     * Record OTP sent time
     */
    public function recordOtpSent(): void
    {
        $this->update(['last_otp_sent_at' => now()]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricData extends Model
{
    protected $fillable = [
        'user_id',
        'biometric_type',
        'biometric_data_encrypted',
        'device_id',
        'device_name',
        'is_verified',
        'is_active',
        'verification_success_count',
        'verification_fail_count',
        'last_verified_at',
        'last_failed_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'last_verified_at' => 'datetime',
        'last_failed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the biometric data
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Encrypt biometric data
     */
    public function encryptBiometricData(string $data): string
    {
        return encrypt($data);
    }

    /**
     * Decrypt biometric data
     */
    public function decryptBiometricData(): string
    {
        try {
            return decrypt($this->biometric_data_encrypted);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt biometric data: '.$e->getMessage());

            return '';
        }
    }

    /**
     * Set and encrypt biometric data
     */
    public function setBiometricData(string $data): void
    {
        $this->update([
            'biometric_data_encrypted' => $this->encryptBiometricData($data),
        ]);
    }

    /**
     * Record successful verification
     */
    public function recordSuccessfulVerification(): void
    {
        $this->increment('verification_success_count');
        $this->update(['last_verified_at' => now()]);
    }

    /**
     * Record failed verification
     */
    public function recordFailedVerification(): void
    {
        $this->increment('verification_fail_count');
        $this->update(['last_failed_at' => now()]);
    }

    /**
     * Get success rate
     */
    public function getSuccessRate(): float
    {
        $total = $this->verification_success_count + $this->verification_fail_count;
        if ($total === 0) {
            return 0;
        }

        return round(($this->verification_success_count / $total) * 100, 2);
    }

    /**
     * Check if too many failures
     */
    public function hasTooManyFailures(int $threshold = 5): bool
    {
        return $this->verification_fail_count >= $threshold;
    }

    /**
     * Deactivate biometric
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate biometric
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Scope for active biometrics
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified biometrics
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific biometric type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('biometric_type', $type);
    }
}

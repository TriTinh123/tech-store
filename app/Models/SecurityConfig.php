<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'auth_level',
        'require_otp',
        'otp_method',
        'otp_expiry_minutes',
        'require_security_questions',
        'security_questions_min_answers',
        'require_device_verification',
        'max_concurrent_devices',
        'max_login_attempts',
        'login_attempt_lockout_minutes',
        'anomaly_detection_enabled',
        'anomaly_detection_threshold',
        'auto_lockout_on_critical',
        'enable_ip_blocking',
        'block_ips_after_failed_attempts',
        'ip_block_duration_minutes',
        'enable_geo_restrictions',
        'allowed_countries',
        'require_new_location_confirmation',
        'allow_concurrent_sessions',
        'max_concurrent_sessions',
        'session_timeout_minutes',
        'notify_on_new_ip',
        'notify_on_new_device',
        'notify_on_failed_attempts',
        'notify_on_account_lockout',
        'enforce_password_expiry_days',
        'require_password_history_count',
        'require_strong_password',
        'enable_biometric_authentication',
        'enable_hardware_key_support',
        'idle_timeout_minutes',
        'suspicious_activity_threshold',
        'log_all_activities',
        'data_retention_days',
        'enable_security_config',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'require_otp' => 'boolean',
        'require_security_questions' => 'boolean',
        'require_device_verification' => 'boolean',
        'anomaly_detection_enabled' => 'boolean',
        'auto_lockout_on_critical' => 'boolean',
        'enable_ip_blocking' => 'boolean',
        'enable_geo_restrictions' => 'boolean',
        'require_new_location_confirmation' => 'boolean',
        'allow_concurrent_sessions' => 'boolean',
        'notify_on_new_ip' => 'boolean',
        'notify_on_new_device' => 'boolean',
        'notify_on_failed_attempts' => 'boolean',
        'notify_on_account_lockout' => 'boolean',
        'enforce_password_expiry_days' => 'boolean',
        'require_strong_password' => 'boolean',
        'enable_biometric_authentication' => 'boolean',
        'enable_hardware_key_support' => 'boolean',
        'log_all_activities' => 'boolean',
        'enable_security_config' => 'boolean',
        'allowed_countries' => 'json',
    ];

    /**
     * Get the admin who created this config
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    /**
     * Get the admin who last updated this config
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_admin_id');
    }

    /**
     * Get current active security config
     */
    public static function getCurrent()
    {
        return static::where('enable_security_config', true)->latest()->first();
    }

    /**
     * Check if strict auth is required
     */
    public function isStrictAuthRequired()
    {
        return in_array($this->auth_level, ['strict', 'ultra']);
    }

    /**
     * Check if IP blocking is enabled
     */
    public function isIpBlockingEnabled()
    {
        return $this->enable_ip_blocking && $this->enable_security_config;
    }

    /**
     * Check if anomaly detection is active
     */
    public function isAnomalyDetectionEnabled()
    {
        return $this->anomaly_detection_enabled && $this->enable_security_config;
    }

    /**
     * Get auth level display name
     */
    public function getAuthLevelLabel()
    {
        return match ($this->auth_level) {
            'basic' => 'Cơ bản (Basic)',
            'standard' => 'Tiêu chuẩn (Standard)',
            'strict' => 'Nghiêm ngặt (Strict)',
            'ultra' => 'Cực cao (Ultra)',
            default => $this->auth_level,
        };
    }

    /**
     * Get OTP method display name
     */
    public function getOtpMethodLabel()
    {
        return match ($this->otp_method) {
            'email' => 'Email',
            'sms' => 'SMS',
            'both' => 'Email & SMS',
            default => $this->otp_method,
        };
    }
}

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-Factor Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for the MFA system.
    |
    */

    /**
     * Whether MFA is enabled globally
     */
    'enabled' => env('MFA_ENABLED', true),

    /**
     * MFA requirement level
     * 'optional' - Users can choose to enable MFA
     * 'required' - All users must enable MFA
     * 'admin_only' - Only admins must enable MFA
     */
    'requirement' => env('MFA_REQUIREMENT', 'optional'),

    /**
     * Maximum verification attempts before blocking
     */
    'max_attempts' => env('MFA_MAX_ATTEMPTS', 5),

    /**
     * Block duration in minutes after exceeding max attempts
     */
    'block_duration' => env('MFA_BLOCK_DURATION', 15),

    /**
     * TOTP Configuration
     */
    'totp' => [
        'enabled' => env('MFA_TOTP_ENABLED', true),
        'issuer' => env('APP_NAME', 'TechStore'),
        'window' => 1, // Number of time windows to check (±1)
    ],

    /**
     * SMS Configuration
     */
    'sms' => [
        'enabled' => env('MFA_SMS_ENABLED', true),
        'provider' => env('MFA_SMS_PROVIDER', 'log'), // log, twilio, nexmo, aws
        'validity' => env('MFA_SMS_VALIDITY', 10), // minutes
    ],

    /**
     * Email Configuration
     */
    'email' => [
        'enabled' => env('MFA_EMAIL_ENABLED', true),
        'validity' => env('MFA_EMAIL_VALIDITY', 15), // minutes
    ],

    /**
     * WebAuthn Configuration
     */
    'webauthn' => [
        'enabled' => env('MFA_WEBAUTHN_ENABLED', true),
        'rp_id' => env('MFA_WEBAUTHN_RP_ID', 'localhost'),
        'rp_name' => env('APP_NAME', 'TechStore'),
    ],

    /**
     * Backup Codes Configuration
     */
    'backup_codes' => [
        'enabled' => true,
        'count' => env('MFA_BACKUP_CODE_COUNT', 10),
        'warning_threshold' => env('MFA_BACKUP_CODE_WARNING', 3),
    ],

    /**
     * Session timeout for MFA verification
     */
    'session_timeout' => env('MFA_SESSION_TIMEOUT', 300), // 5 minutes

];

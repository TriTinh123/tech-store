<?php

return [
    // OTP Configuration
    'otp' => [
        'length' => env('3FA_OTP_LENGTH', 6),
        'ttl' => env('3FA_OTP_TTL', 600), // seconds (10 minutes)
        'max_attempts' => env('3FA_OTP_MAX_ATTEMPTS', 5),
    ],

    // Security Questions Configuration
    'security_questions' => [
        'count' => env('3FA_SECURITY_QUESTION_COUNT', 3),
        'max_attempts' => env('3FA_SECURITY_ANSWER_ATTEMPTS', 3),
    ],

    // Biometric Configuration
    'biometric' => [
        'enabled' => env('3FA_BIOMETRIC_ENABLED', true),
        'types' => ['fingerprint', 'face_id', 'iris_scan'],
        'failure_threshold' => env('3FA_BIOMETRIC_FAILURE_THRESHOLD', 5),
        'similarity_threshold' => env('3FA_BIOMETRIC_SIMILARITY_THRESHOLD', 0.95),
    ],

    // SMS Configuration
    'sms' => [
        'provider' => env('3FA_SMS_PROVIDER', 'twilio'), // twilio, sns, nexmo
        'enabled' => env('3FA_SMS_ENABLED', false),
        'rate_limit' => env('3FA_SMS_RATE_LIMIT', 60), // seconds between OTP sends
    ],

    // Session Configuration
    'session' => [
        'otp_verified_timeout' => env('3FA_OTP_VERIFIED_TIMEOUT', 3600), // 1 hour
        'allow_remember_device' => env('3FA_ALLOW_REMEMBER_DEVICE', true),
        'device_remember_days' => env('3FA_DEVICE_REMEMBER_DAYS', 30),
    ],

    // Logging & Auditing
    'logging' => [
        'log_attempts' => env('3FA_LOG_ATTEMPTS', true),
        'log_retention_days' => env('3FA_LOG_RETENTION_DAYS', 90),
    ],

    // Email Configuration
    'email' => [
        'from' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name' => env('MAIL_FROM_NAME', config('app.name')),
        'send_otp' => env('3FA_SEND_OTP_EMAIL', true),
    ],

    // Feature Flags
    'features' => [
        'enable_otp' => env('3FA_ENABLE_OTP', true),
        'enable_security_questions' => env('3FA_ENABLE_SECURITY_QUESTIONS', true),
        'enable_biometric' => env('3FA_ENABLE_BIOMETRIC', true),
        'require_on_suspicious_login' => env('3FA_REQUIRE_SUSPICIOUS_LOGIN', true),
        'require_on_new_device' => env('3FA_REQUIRE_NEW_DEVICE', true),
        'require_on_new_location' => env('3FA_REQUIRE_NEW_LOCATION', true),
    ],
];

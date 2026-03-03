<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Provider
    |--------------------------------------------------------------------------
    |
    | Specify which SMS provider to use for sending notifications.
    | Supported: "twilio", "aws_sns", "vonage"
    |
    */
    'sms_provider' => env('SMS_PROVIDER', 'twilio'),

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from_number' => env('TWILIO_FROM_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AWS SNS Configuration
    |--------------------------------------------------------------------------
    */
    'aws_sns' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Vonage (Nexmo) Configuration
    |--------------------------------------------------------------------------
    */
    'vonage' => [
        'api_key' => env('VONAGE_API_KEY'),
        'api_secret' => env('VONAGE_API_SECRET'),
        'from' => env('VONAGE_FROM', 'Laravel'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    */
    'email' => [
        'from_address' => env('MAIL_FROM_ADDRESS', 'security@example.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Security Team'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Retention
    |--------------------------------------------------------------------------
    */
    'retention' => [
        'notifications_days' => env('NOTIFICATION_RETENTION_DAYS', 90),
        'logs_days' => env('NOTIFICATION_LOG_RETENTION_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Configuration
    |--------------------------------------------------------------------------
    | Configure real-time notifications via WebSockets
    */
    'broadcast' => [
        'enabled' => env('NOTIFICATION_BROADCAST_ENABLED', false),
        'driver' => env('NOTIFICATION_BROADCAST_DRIVER', 'pusher'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Rate Limiting
    |--------------------------------------------------------------------------
    | Prevent notification spam
    */
    'rate_limit' => [
        'enabled' => true,
        'max_per_hour' => 20,
        'max_per_day' => 100,
    ],
];

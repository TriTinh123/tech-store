<?php

return [
    'bank_transfer' => [
        'enabled' => env('BANK_TRANSFER_ENABLED', true),
        'bank_name' => env('BANK_NAME', 'Vietcombank'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
        'account_holder' => env('BANK_ACCOUNT_HOLDER', 'Your Company Name'),
        'bin' => env('BANK_BIN', '970436'),
    ],

    // Stripe Configuration
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    // PayPal Configuration
    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
    ],

    // GHN Shipping Configuration
    'ghn' => [
        'token' => env('GHN_API_TOKEN'),
        'shop_id' => env('GHN_SHOP_ID'),
        'from_district' => env('GHN_FROM_DISTRICT', 1442),
        'pick_station_id' => env('GHN_PICK_STATION_ID'),
    ],

    // Grab Shipping Configuration
    'grab' => [
        'access_token' => env('GRAB_ACCESS_TOKEN'),
        'partner_id' => env('GRAB_PARTNER_ID'),
        'pickup_address' => env('GRAB_PICKUP_ADDRESS', ''),
        'pickup_lat' => env('GRAB_PICKUP_LAT', 10.7769),
        'pickup_lng' => env('GRAB_PICKUP_LNG', 106.6826),
    ],
];

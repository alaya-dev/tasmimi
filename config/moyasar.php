<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Moyasar API Keys
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Moyasar API keys. These keys are used to
    | authenticate with the Moyasar API.
    |
    */

    'publishable_key' => env('MOYASAR_API_PUBLISHABLE_KEY'),
    'secret_key' => env('MOYASAR_API_SECRET_KEY'),
    'webhook_secret' => env('MOYASAR_WEBHOOK_SECRET'),
    'finish_payment_url' => env('FINISH_PAYMENT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Moyasar API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the base URL for the Moyasar API.
    |
    */

    'base_url' => env('MOYASAR_BASE_URL', 'https://api.moyasar.com/v1/'),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificates when making API calls.
    | Should be true in production.
    |
    */

    'verify_ssl' => env('APP_ENV') === 'production',
];

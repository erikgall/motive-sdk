<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | The default Motive API connection to use. This corresponds to one of
    | the connections defined in the "connections" array below.
    |
    */

    'default' => env('MOTIVE_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | API Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure multiple API connections. This is useful for
    | multi-tenant applications where each tenant has their own API key.
    |
    */

    'connections' => [
        'default' => [
            'auth_driver' => env('MOTIVE_AUTH_DRIVER', 'api_key'),
            'api_key'     => env('MOTIVE_API_KEY'),
            'oauth'       => [
                'client_id'     => env('MOTIVE_CLIENT_ID'),
                'client_secret' => env('MOTIVE_CLIENT_SECRET'),
                'redirect_uri'  => env('MOTIVE_REDIRECT_URI'),
            ],
            'base_url' => env('MOTIVE_BASE_URL', 'https://api.gomotive.com'),
            'timeout'  => (int) env('MOTIVE_TIMEOUT', 30),
            'retry'    => [
                'times' => (int) env('MOTIVE_RETRY_TIMES', 3),
                'sleep' => (int) env('MOTIVE_RETRY_SLEEP', 100),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Headers
    |--------------------------------------------------------------------------
    |
    | These headers will be sent with every API request. The timezone header
    | is used for date/time formatting in responses.
    |
    */

    'headers' => [
        'timezone'     => env('MOTIVE_TIMEZONE'),
        'metric_units' => (bool) env('MOTIVE_METRIC_UNITS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook signature verification settings. The secret is used
    | to verify incoming webhooks, and tolerance is the max age in seconds.
    |
    */

    'webhooks' => [
        'secret'    => env('MOTIVE_WEBHOOK_SECRET'),
        'tolerance' => (int) env('MOTIVE_WEBHOOK_TOLERANCE', 300),
    ],
];

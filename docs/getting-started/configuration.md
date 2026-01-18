# Configuration

## Environment Variables

Add your Motive API credentials to your `.env` file:

```env
# API Key Authentication (simplest method)
MOTIVE_API_KEY=your-api-key

# OAuth Authentication (optional)
MOTIVE_CLIENT_ID=your-client-id
MOTIVE_CLIENT_SECRET=your-client-secret
MOTIVE_REDIRECT_URI=https://your-app.com/motive/callback

# Webhook Secret (optional, for verifying webhook signatures)
MOTIVE_WEBHOOK_SECRET=your-webhook-secret

# Optional Settings
MOTIVE_TIMEZONE=America/Chicago
MOTIVE_METRIC_UNITS=false
```

## Configuration File

After publishing, the configuration file is located at `config/motive.php`:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | The default connection to use when making API requests.
    |
    */
    'default' => env('MOTIVE_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | Configure one or more API connections. Each connection can have its
    | own authentication settings, base URL, timeout, and retry configuration.
    |
    */
    'connections' => [
        'default' => [
            'auth_driver' => env('MOTIVE_AUTH_DRIVER', 'api_key'),
            'api_key' => env('MOTIVE_API_KEY'),
            'oauth' => [
                'client_id' => env('MOTIVE_CLIENT_ID'),
                'client_secret' => env('MOTIVE_CLIENT_SECRET'),
                'redirect_uri' => env('MOTIVE_REDIRECT_URI'),
            ],
            'base_url' => env('MOTIVE_BASE_URL', 'https://api.gomotive.com'),
            'timeout' => 30,
            'retry' => [
                'times' => 3,
                'sleep' => 100,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Headers
    |--------------------------------------------------------------------------
    |
    | Headers to include with every API request.
    |
    */
    'headers' => [
        'timezone' => env('MOTIVE_TIMEZONE'),
        'metric_units' => env('MOTIVE_METRIC_UNITS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    |
    | Configuration for webhook signature verification.
    |
    */
    'webhooks' => [
        'secret' => env('MOTIVE_WEBHOOK_SECRET'),
        'tolerance' => 300, // seconds
    ],
];
```

## Configuration Options

### Connection Settings

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `auth_driver` | string | `api_key` | Authentication method: `api_key` or `oauth` |
| `api_key` | string | `null` | Your Motive API key |
| `oauth` | array | `[]` | OAuth configuration (client_id, client_secret, redirect_uri) |
| `base_url` | string | `https://api.gomotive.com` | API base URL |
| `timeout` | int | `30` | Request timeout in seconds |
| `retry.times` | int | `3` | Number of retry attempts for failed requests |
| `retry.sleep` | int | `100` | Milliseconds between retry attempts |

### Header Settings

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `timezone` | string | `null` | Timezone for datetime responses (e.g., `America/Chicago`) |
| `metric_units` | bool | `false` | Use metric units for distances and volumes |

### Webhook Settings

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `secret` | string | `null` | Secret key for verifying webhook signatures |
| `tolerance` | int | `300` | Maximum age of webhook payload in seconds |

## Getting Your API Key

1. Log in to your [Motive Dashboard](https://gomotive.com)
2. Navigate to **Settings** > **API Access**
3. Generate a new API key
4. Copy the key to your `.env` file

> **Security Note**: Never commit your API keys to version control. Always use environment variables.

## Multiple Connections

You can configure multiple connections for multi-tenant applications:

```php
'connections' => [
    'default' => [
        'auth_driver' => 'api_key',
        'api_key' => env('MOTIVE_API_KEY'),
    ],
    'company-a' => [
        'auth_driver' => 'api_key',
        'api_key' => env('MOTIVE_COMPANY_A_API_KEY'),
    ],
    'company-b' => [
        'auth_driver' => 'api_key',
        'api_key' => env('MOTIVE_COMPANY_B_API_KEY'),
    ],
],
```

See [Multi-Tenancy](../authentication/multi-tenancy.md) for detailed usage.

## Next Steps

- [Follow the quick start guide](quick-start.md)
- [Learn about authentication options](../authentication/api-key.md)

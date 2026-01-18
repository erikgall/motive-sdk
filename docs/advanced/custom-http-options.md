# Custom HTTP Options

Configure HTTP client options for specific requests or globally.

## Per-Request Options

### Timeout

Override the default timeout for a single request:

```php
use Motive\Facades\Motive;

// Long-running report generation
$report = Motive::withOptions(['timeout' => 120])
    ->iftaReports()
    ->generate([
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31',
    ]);
```

### Connect Timeout

Set a specific connection timeout:

```php
$vehicles = Motive::withOptions(['connect_timeout' => 5])
    ->vehicles()
    ->list();
```

## Global Configuration

### Config File

Set default HTTP options in your config:

```php
// config/motive.php
return [
    'api_key' => env('MOTIVE_API_KEY'),

    'http' => [
        'timeout' => 30,
        'connect_timeout' => 10,
        'retry' => [
            'times' => 3,
            'sleep' => 100,
        ],
    ],
];
```

## Retry Configuration

### Automatic Retries

Configure retry behavior for failed requests:

```php
// Global config
'http' => [
    'retry' => [
        'times' => 3,      // Number of retries
        'sleep' => 100,    // Milliseconds between retries
        'when' => null,    // Optional callback
    ],
],
```

### Per-Request Retry

```php
$vehicles = Motive::withOptions([
    'retry' => [
        'times' => 5,
        'sleep' => 500,
    ],
])->vehicles()->list();
```

## SSL/TLS Options

### Verify Certificates

```php
// Disable verification (not recommended for production)
$response = Motive::withOptions(['verify' => false])
    ->get('/v1/vehicles');

// Custom CA bundle
$response = Motive::withOptions(['verify' => '/path/to/cacert.pem'])
    ->get('/v1/vehicles');
```

## Proxy Configuration

### HTTP Proxy

```php
$vehicles = Motive::withOptions([
    'proxy' => 'http://proxy.example.com:8080',
])->vehicles()->list();
```

### Proxy with Authentication

```php
$vehicles = Motive::withOptions([
    'proxy' => 'http://user:password@proxy.example.com:8080',
])->vehicles()->list();
```

## Headers

### Custom Headers

```php
$vehicles = Motive::withOptions([
    'headers' => [
        'X-Custom-Header' => 'value',
        'X-Request-Id' => uniqid(),
    ],
])->vehicles()->list();
```

## Debugging

### Debug Mode

Enable HTTP debugging:

```php
$vehicles = Motive::withOptions(['debug' => true])
    ->vehicles()
    ->list();
```

### Custom Logger

```php
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;

$vehicles = Motive::withOptions([
    'on_stats' => function ($stats) {
        logger()->debug('Request stats', [
            'uri' => (string) $stats->getEffectiveUri(),
            'time' => $stats->getTransferTime(),
        ]);
    },
])->vehicles()->list();
```

## Chaining with Other Modifiers

HTTP options can be combined with other context modifiers:

```php
$vehicles = Motive::connection('company-a')
    ->withTimezone('America/Chicago')
    ->withOptions(['timeout' => 60])
    ->vehicles()
    ->list();
```

## Common Use Cases

### Large Data Exports

```php
// Increase timeout for large exports
$report = Motive::withOptions([
    'timeout' => 300,        // 5 minutes
    'connect_timeout' => 30,
])->get('/v1/reports/large_export');
```

### Unstable Networks

```php
// More aggressive retries
$vehicles = Motive::withOptions([
    'timeout' => 10,
    'retry' => [
        'times' => 5,
        'sleep' => 1000,
    ],
])->vehicles()->list();
```

### Development/Testing

```php
// Quick timeout for fast feedback
$vehicles = Motive::withOptions([
    'timeout' => 5,
    'connect_timeout' => 2,
])->vehicles()->list();
```

## Environment-Based Configuration

### Different Settings Per Environment

```php
// config/motive.php
return [
    'http' => [
        'timeout' => env('MOTIVE_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('MOTIVE_CONNECT_TIMEOUT', 10),
        'verify' => env('MOTIVE_SSL_VERIFY', true),
        'retry' => [
            'times' => env('MOTIVE_RETRY_TIMES', 3),
            'sleep' => env('MOTIVE_RETRY_SLEEP', 100),
        ],
    ],
];
```

### Environment Variables

```env
# .env.local (development)
MOTIVE_HTTP_TIMEOUT=10
MOTIVE_RETRY_TIMES=1

# .env.production
MOTIVE_HTTP_TIMEOUT=30
MOTIVE_RETRY_TIMES=3
```

## Available Options

| Option | Type | Description |
|--------|------|-------------|
| `timeout` | `int` | Request timeout in seconds |
| `connect_timeout` | `int` | Connection timeout in seconds |
| `retry.times` | `int` | Number of retry attempts |
| `retry.sleep` | `int` | Milliseconds between retries |
| `verify` | `bool\|string` | SSL certificate verification |
| `proxy` | `string` | Proxy server URL |
| `headers` | `array` | Additional HTTP headers |
| `debug` | `bool` | Enable debug output |
| `on_stats` | `callable` | Stats callback function |

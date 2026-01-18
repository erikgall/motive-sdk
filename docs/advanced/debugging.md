# Debugging

Tools and techniques for debugging Motive SDK integrations.

## Logging Requests

### Enable Request Logging

```php
use Motive\Facades\Motive;

// Log all requests in development
if (app()->environment('local')) {
    Motive::withOptions([
        'on_stats' => function ($stats) {
            logger()->debug('Motive API Request', [
                'method' => $stats->getRequest()->getMethod(),
                'uri' => (string) $stats->getEffectiveUri(),
                'time' => $stats->getTransferTime(),
                'status' => $stats->getResponse()?->getStatusCode(),
            ]);
        },
    ]);
}
```

### Custom Request Logger

```php
// app/Support/MotiveLogger.php
namespace App\Support;

use Illuminate\Support\Facades\Log;

class MotiveLogger
{
    public static function logRequest($stats): void
    {
        $request = $stats->getRequest();
        $response = $stats->getResponse();

        Log::channel('motive')->info('API Request', [
            'method' => $request->getMethod(),
            'uri' => (string) $stats->getEffectiveUri(),
            'request_headers' => $request->getHeaders(),
            'response_status' => $response?->getStatusCode(),
            'response_headers' => $response?->getHeaders(),
            'transfer_time' => $stats->getTransferTime(),
        ]);
    }
}
```

## Exception Details

### Accessing Full Error Information

```php
use Motive\Exceptions\MotiveException;

try {
    $vehicle = Motive::vehicles()->find(999);
} catch (MotiveException $e) {
    // Basic info
    echo $e->getMessage();
    echo $e->getCode(); // HTTP status code

    // Full response details
    $response = $e->getResponse();
    $body = $e->getResponseBody();

    logger()->error('Motive API Error', [
        'message' => $e->getMessage(),
        'status' => $e->getCode(),
        'body' => $body,
    ]);
}
```

### Exception Types

```php
use Motive\Exceptions\AuthenticationException;
use Motive\Exceptions\AuthorizationException;
use Motive\Exceptions\ValidationException;
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\RateLimitException;
use Motive\Exceptions\ServerException;

try {
    // API call
} catch (AuthenticationException $e) {
    // 401 - Check API key
    logger()->warning('Authentication failed', ['error' => $e->getMessage()]);
} catch (AuthorizationException $e) {
    // 403 - Check permissions
    logger()->warning('Authorization denied', ['error' => $e->getMessage()]);
} catch (ValidationException $e) {
    // 422 - Check request data
    logger()->info('Validation failed', ['errors' => $e->errors()]);
} catch (NotFoundException $e) {
    // 404 - Resource not found
    logger()->info('Resource not found', ['error' => $e->getMessage()]);
} catch (RateLimitException $e) {
    // 429 - Rate limited
    logger()->warning('Rate limited', ['retry_after' => $e->retryAfter()]);
} catch (ServerException $e) {
    // 5xx - Motive server error
    logger()->error('Server error', ['error' => $e->getMessage()]);
}
```

## Debug Mode

### Enable HTTP Debug Output

```php
$vehicles = Motive::withOptions(['debug' => true])
    ->vehicles()
    ->list();
```

This outputs raw HTTP request/response data to STDERR.

### Debug to File

```php
$debugFile = fopen(storage_path('logs/motive-debug.txt'), 'a');

$vehicles = Motive::withOptions(['debug' => $debugFile])
    ->vehicles()
    ->list();

fclose($debugFile);
```

## Inspecting Requests in Tests

### Using the Fake Client

```php
use Motive\Facades\Motive;

public function test_debugging_requests(): void
{
    Motive::fake();

    // Your code
    $service = new MyService();
    $service->syncVehicles();

    // Inspect all recorded requests
    $requests = Motive::recorded();

    foreach ($requests as $request) {
        dump([
            'method' => $request['method'],
            'path' => $request['path'],
            'query' => $request['query'],
            'data' => $request['data'],
            'timestamp' => $request['timestamp'],
        ]);
    }
}
```

## Response Inspection

### Examining Response Structure

```php
$response = Motive::get('/v1/vehicles');

// Full response inspection
dump([
    'status' => $response->status(),
    'headers' => $response->headers(),
    'body' => $response->body(),
    'json' => $response->json(),
]);
```

### Check Response Keys

```php
$response = Motive::get('/v1/vehicles');
$json = $response->json();

// List available keys
dump(array_keys($json));

// Check pagination structure
dump($json['pagination'] ?? 'No pagination');
```

## Common Issues

### Authentication Problems

```php
// Verify API key is set
dump(config('motive.api_key'));

// Check connection settings
dump(config('motive.connections.' . config('motive.default')));
```

### Wrong API Version

```php
// Check resource API version
$resource = Motive::vehicles();
dump($resource->apiVersion ?? 'default');
```

### Pagination Issues

```php
// Debug pagination
$paginator = Motive::vehicles()->paginate(['per_page' => 10]);

foreach ($paginator as $vehicle) {
    dump([
        'current_page' => $paginator->currentPage(),
        'has_more' => $paginator->hasMorePages(),
        'vehicle_id' => $vehicle->id,
    ]);
}
```

## Performance Debugging

### Timing Requests

```php
$start = microtime(true);

$vehicles = Motive::vehicles()->list();

$duration = microtime(true) - $start;

logger()->debug("Vehicle list took {$duration}s");
```

### Memory Usage

```php
$memBefore = memory_get_usage();

// Use lazy pagination for large datasets
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    // Process
}

$memAfter = memory_get_usage();
$memUsed = ($memAfter - $memBefore) / 1024 / 1024;

logger()->debug("Memory used: {$memUsed}MB");
```

## Telescope Integration

If using Laravel Telescope, Motive API calls will be logged as HTTP requests.

```php
// config/telescope.php
'watchers' => [
    Watchers\RequestWatcher::class => [
        'enabled' => env('TELESCOPE_REQUEST_WATCHER', true),
        'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
    ],
],
```

## Creating a Debug Helper

```php
// app/Support/MotiveDebug.php
namespace App\Support;

use Motive\Facades\Motive;

class MotiveDebug
{
    public static function testConnection(): array
    {
        try {
            $company = Motive::companies()->current();

            return [
                'success' => true,
                'company' => $company->name ?? 'Unknown',
                'auth' => 'Valid',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'type' => get_class($e),
            ];
        }
    }

    public static function dumpConfig(): array
    {
        return [
            'default_connection' => config('motive.default'),
            'api_key_set' => !empty(config('motive.api_key')),
            'http_timeout' => config('motive.http.timeout'),
            'base_url' => config('motive.base_url'),
        ];
    }
}
```

### Usage

```php
// In tinker or a debug route
dump(MotiveDebug::dumpConfig());
dump(MotiveDebug::testConnection());
```

## Artisan Debug Command

```php
// app/Console/Commands/MotiveDebugCommand.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Motive\Facades\Motive;

class MotiveDebugCommand extends Command
{
    protected $signature = 'motive:debug';
    protected $description = 'Debug Motive SDK configuration';

    public function handle(): void
    {
        $this->info('Motive SDK Debug');
        $this->newLine();

        // Config
        $this->table(['Setting', 'Value'], [
            ['API Key Set', config('motive.api_key') ? 'Yes' : 'No'],
            ['Default Connection', config('motive.default')],
            ['Timeout', config('motive.http.timeout', 30)],
        ]);

        // Test connection
        $this->info('Testing connection...');

        try {
            $company = Motive::companies()->current();
            $this->info("Connected to: {$company->name}");
        } catch (\Exception $e) {
            $this->error("Connection failed: {$e->getMessage()}");
        }
    }
}
```

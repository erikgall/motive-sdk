# Raw API Requests

Make direct HTTP requests to the Motive API for endpoints not covered by the SDK or for custom integrations.

## Making Raw Requests

### GET Request

```php
use Motive\Facades\Motive;

$response = Motive::get('/v1/custom_endpoint', [
    'param1' => 'value1',
    'param2' => 'value2',
]);

$data = $response->json();
$status = $response->status();
```

### POST Request

```php
$response = Motive::post('/v1/custom_endpoint', [
    'field1' => 'value1',
    'field2' => 'value2',
]);

if ($response->successful()) {
    $data = $response->json();
}
```

### PUT Request

```php
$response = Motive::put('/v1/custom_endpoint/123', [
    'field1' => 'updated_value',
]);
```

### PATCH Request

```php
$response = Motive::patch('/v1/custom_endpoint/123', [
    'field1' => 'partial_update',
]);
```

### DELETE Request

```php
$response = Motive::delete('/v1/custom_endpoint/123');

if ($response->successful()) {
    echo "Deleted successfully";
}
```

## Response Object

The raw request methods return a `Response` object:

```php
$response = Motive::get('/v1/vehicles');

// Check status
$response->successful();  // 2xx status
$response->failed();      // 4xx or 5xx status
$response->status();      // HTTP status code

// Get data
$response->json();        // Decoded JSON body
$response->json('vehicles'); // Specific key from JSON
$response->body();        // Raw body string

// Headers
$response->headers();     // All headers
$response->header('X-RateLimit-Remaining');
```

## Working with Responses

### JSON Data

```php
$response = Motive::get('/v1/vehicles');

// Get all data
$data = $response->json();

// Get specific key
$vehicles = $response->json('vehicles');

// Nested access
$firstVehicle = $response->json('vehicles.0');
```

### Error Handling

```php
use Motive\Exceptions\MotiveException;

try {
    $response = Motive::get('/v1/custom_endpoint');

    if ($response->failed()) {
        throw new \RuntimeException("Request failed: " . $response->status());
    }

    return $response->json();
} catch (MotiveException $e) {
    // Handle API errors
    logger()->error('API Error', [
        'message' => $e->getMessage(),
        'status' => $e->getCode(),
    ]);
}
```

## Using Context Modifiers

Raw requests respect context modifiers:

```php
// With specific connection
$response = Motive::connection('company-a')
    ->get('/v1/custom_endpoint');

// With timezone
$response = Motive::withTimezone('America/Chicago')
    ->get('/v1/hos_logs');

// With user context
$response = Motive::withUserId($user->id)
    ->post('/v1/custom_endpoint', $data);

// With custom HTTP options
$response = Motive::withOptions(['timeout' => 60])
    ->get('/v1/large_report');
```

## API Versioning

Specify different API versions:

```php
// V1 endpoint
$response = Motive::get('/v1/vehicles');

// V2 endpoint
$response = Motive::get('/v2/webhooks');
```

## Pagination with Raw Requests

Handle pagination manually:

```php
$allVehicles = [];
$page = 1;
$perPage = 100;

do {
    $response = Motive::get('/v1/vehicles', [
        'page' => $page,
        'per_page' => $perPage,
    ]);

    $data = $response->json();
    $vehicles = $data['vehicles'] ?? [];
    $allVehicles = array_merge($allVehicles, $vehicles);

    $hasMore = $data['pagination']['has_more_pages'] ?? false;
    $page++;
} while ($hasMore);

return $allVehicles;
```

## File Uploads

Upload files using raw requests:

```php
use Illuminate\Http\UploadedFile;

$file = $request->file('document');

$response = Motive::post('/v1/documents', [
    'file' => fopen($file->path(), 'r'),
    'type' => 'proof_of_delivery',
    'dispatch_id' => 123,
]);
```

## Combining with Resources

Use raw requests alongside typed resources:

```php
// Use typed resource for standard operations
$vehicle = Motive::vehicles()->find(123);

// Use raw request for custom endpoint
$customData = Motive::get("/v1/vehicles/{$vehicle->id}/custom_report")->json();

// Combine the data
return [
    'vehicle' => $vehicle,
    'custom_report' => $customData,
];
```

## Use Cases

### Custom Reports

```php
$response = Motive::get('/v1/reports/custom', [
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'metrics' => ['mileage', 'fuel', 'hours'],
]);

return $response->json('report');
```

### Bulk Operations

```php
$response = Motive::post('/v1/vehicles/bulk_update', [
    'vehicle_ids' => [1, 2, 3, 4, 5],
    'update' => [
        'status' => 'inactive',
    ],
]);

$results = $response->json('results');
```

### Beta/Preview Endpoints

```php
// Access preview features
$response = Motive::get('/v2-preview/new_feature', [
    'include_beta' => true,
]);
```

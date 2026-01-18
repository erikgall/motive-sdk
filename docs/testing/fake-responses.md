# Fake Responses

Build custom API responses for testing various scenarios.

## FakeResponse Class

The `FakeResponse` class provides a fluent interface for building fake API responses.

```php
use Motive\Testing\FakeResponse;
```

## Static Constructors

### json()

Create a successful JSON response:

```php
$response = FakeResponse::json([
    'vehicle' => [
        'id' => 123,
        'number' => 'TRUCK-001',
    ],
]);

// With custom status code
$response = FakeResponse::json(['data' => 'value'], 201);
```

### empty()

Create an empty response:

```php
$response = FakeResponse::empty();

// With custom status
$response = FakeResponse::empty(204);
```

### paginated()

Create a paginated response:

```php
$response = FakeResponse::paginated(
    items: [
        ['id' => 1, 'number' => 'TRUCK-001'],
        ['id' => 2, 'number' => 'TRUCK-002'],
    ],
    total: 50,
    perPage: 25,
    key: 'vehicles',
    currentPage: 1
);

// Response structure:
// {
//     "vehicles": [...],
//     "pagination": {
//         "total": 50,
//         "per_page": 25,
//         "current_page": 1,
//         "last_page": 2,
//         "has_more_pages": true
//     }
// }
```

## Error Responses

### unauthorized()

Create a 401 Unauthorized response:

```php
$response = FakeResponse::unauthorized();

// With custom message
$response = FakeResponse::unauthorized('Invalid API key');
```

### forbidden()

Create a 403 Forbidden response:

```php
$response = FakeResponse::forbidden();

// With custom message
$response = FakeResponse::forbidden('Insufficient permissions');
```

### notFound()

Create a 404 Not Found response:

```php
$response = FakeResponse::notFound();

// With custom message
$response = FakeResponse::notFound('Vehicle not found');
```

### validationError()

Create a 422 Validation Error response:

```php
$response = FakeResponse::validationError([
    'number' => ['Vehicle number is required'],
    'vin' => ['VIN must be 17 characters'],
]);

// Response structure:
// {
//     "message": "Validation failed",
//     "errors": {
//         "number": ["Vehicle number is required"],
//         "vin": ["VIN must be 17 characters"]
//     }
// }
```

### rateLimit()

Create a 429 Rate Limit response:

```php
$response = FakeResponse::rateLimit();

// With custom retry-after
$response = FakeResponse::rateLimit(120);

// Response structure:
// {
//     "error": "Rate limit exceeded",
//     "retry_after": 120
// }
```

### serverError()

Create a 500 Server Error response:

```php
$response = FakeResponse::serverError();

// With custom message
$response = FakeResponse::serverError('Database connection failed');
```

### error()

Create a generic error response:

```php
$response = FakeResponse::error(418, [
    'error' => "I'm a teapot",
]);
```

## Adding Headers

```php
$response = FakeResponse::json(['data' => 'value'])
    ->withHeaders([
        'X-RateLimit-Remaining' => '99',
        'X-Request-Id' => 'abc-123',
    ]);
```

## Using with Motive::fake()

### Basic Usage

```php
use Motive\Facades\Motive;
use Motive\Testing\FakeResponse;

Motive::fake([
    'vehicles' => FakeResponse::json([
        'vehicles' => [
            ['id' => 1, 'number' => 'TRUCK-001'],
        ],
    ]),
]);
```

### Mixed Responses

```php
Motive::fake([
    'vehicles' => FakeResponse::paginated(
        items: Vehicle::factory()->count(10)->raw(),
        total: 100,
        perPage: 10,
        key: 'vehicles'
    ),
    'users' => FakeResponse::json([
        'users' => User::factory()->count(5)->raw(),
    ]),
    'dispatches' => FakeResponse::notFound(),
]);
```

### Error Scenarios

```php
// Test authentication failure
Motive::fake([
    'vehicles' => FakeResponse::unauthorized(),
]);

// Test rate limiting
Motive::fake([
    'vehicles' => FakeResponse::rateLimit(60),
]);

// Test validation errors
Motive::fake([
    'vehicles' => FakeResponse::validationError([
        'number' => ['Already exists'],
    ]),
]);
```

## Response Sequences

Test different responses on subsequent calls:

```php
$fake = new \Motive\Testing\MotiveFake();

// First call succeeds, second call fails
$fake->fakeSequence('/v1/vehicles', [
    FakeResponse::json(['vehicles' => [['id' => 1]]]),
    FakeResponse::rateLimit(60),
]);

// First call: returns vehicle
$response1 = $fake->get('/v1/vehicles');

// Second call: returns rate limit error
$response2 = $fake->get('/v1/vehicles');
```

### Testing Retry Logic

```php
$fake = new \Motive\Testing\MotiveFake();

$fake->fakeSequence('/v1/vehicles', [
    FakeResponse::rateLimit(1),
    FakeResponse::rateLimit(1),
    FakeResponse::json(['vehicles' => [['id' => 1]]]),
]);

// Test that your code retries and eventually succeeds
```

### Testing Pagination

```php
$fake = new \Motive\Testing\MotiveFake();

$fake->fakeSequence('/v1/vehicles', [
    FakeResponse::paginated(
        items: [['id' => 1], ['id' => 2]],
        total: 4,
        perPage: 2,
        key: 'vehicles',
        currentPage: 1
    ),
    FakeResponse::paginated(
        items: [['id' => 3], ['id' => 4]],
        total: 4,
        perPage: 2,
        key: 'vehicles',
        currentPage: 2
    ),
]);
```

## Testing Specific Scenarios

### Testing Not Found

```php
public function test_handles_vehicle_not_found(): void
{
    Motive::fake([
        'vehicles/999' => FakeResponse::notFound('Vehicle not found'),
    ]);

    $this->expectException(NotFoundException::class);

    Motive::vehicles()->find(999);
}
```

### Testing Validation

```php
public function test_handles_validation_errors(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::validationError([
            'vin' => ['VIN is invalid'],
        ]),
    ]);

    try {
        Motive::vehicles()->create(['vin' => 'invalid']);
        $this->fail('Expected ValidationException');
    } catch (ValidationException $e) {
        $this->assertArrayHasKey('vin', $e->errors());
    }
}
```

### Testing Rate Limits

```php
public function test_handles_rate_limit(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::rateLimit(60),
    ]);

    try {
        Motive::vehicles()->list();
        $this->fail('Expected RateLimitException');
    } catch (RateLimitException $e) {
        $this->assertEquals(60, $e->retryAfter());
    }
}
```

### Testing Server Errors

```php
public function test_handles_server_error(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::serverError('Database unavailable'),
    ]);

    $this->expectException(ServerException::class);

    Motive::vehicles()->list();
}
```

## Custom Response Building

### Dynamic Responses

```php
$vehicleId = 123;

Motive::fake([
    "vehicles/{$vehicleId}" => FakeResponse::json([
        'vehicle' => Vehicle::factory()->make(['id' => $vehicleId])->toArray(),
    ]),
]);
```

### Conditional Responses

```php
$responses = [];

if ($shouldSucceed) {
    $responses['vehicles'] = FakeResponse::json([
        'vehicles' => Vehicle::factory()->count(5)->raw(),
    ]);
} else {
    $responses['vehicles'] = FakeResponse::serverError();
}

Motive::fake($responses);
```

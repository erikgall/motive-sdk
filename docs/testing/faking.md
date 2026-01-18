# Faking the Motive Client

Replace the real Motive client with a fake implementation for testing.

## Basic Faking

### Empty Fake

Create a fake that returns empty responses:

```php
use Motive\Facades\Motive;

public function test_example(): void
{
    Motive::fake();

    $vehicles = Motive::vehicles()->list();

    $this->assertEmpty($vehicles);
}
```

### Fake with Data

Provide fake data to be returned:

```php
use Motive\Data\Vehicle;
use Motive\Data\User;

Motive::fake([
    'vehicles' => [
        Vehicle::factory()->make(['number' => 'TRUCK-001']),
        Vehicle::factory()->make(['number' => 'TRUCK-002']),
    ],
    'users' => [
        User::factory()->make(['first_name' => 'John']),
    ],
]);
```

## Path-Based Faking

### Exact Paths

Fake specific API endpoints:

```php
Motive::fake([
    '/v1/vehicles' => [Vehicle::factory()->make()],
    '/v1/users' => [User::factory()->make()],
]);
```

### Wildcard Paths

Use wildcards for dynamic paths:

```php
Motive::fake([
    '/v1/vehicles/*' => Vehicle::factory()->make(),
    '/v1/users/*/hos_logs' => [HosLog::factory()->make()],
]);
```

## Using FakeResponse

For more control over responses, use `FakeResponse`:

```php
use Motive\Testing\FakeResponse;

Motive::fake([
    'vehicles' => FakeResponse::json([
        'vehicles' => [
            ['id' => 1, 'number' => 'TRUCK-001'],
        ],
    ]),
]);
```

### Error Responses

```php
Motive::fake([
    'vehicles' => FakeResponse::notFound(),
]);

Motive::fake([
    'vehicles' => FakeResponse::unauthorized(),
]);

Motive::fake([
    'vehicles' => FakeResponse::rateLimit(60),
]);
```

## Sequence of Responses

Return different responses on subsequent calls:

```php
use Motive\Testing\FakeResponse;

$fake = new \Motive\Testing\MotiveFake();

$fake->fakeSequence('/v1/vehicles', [
    FakeResponse::json(['vehicles' => [['id' => 1]]]),
    FakeResponse::json(['vehicles' => [['id' => 2]]]),
    FakeResponse::notFound(),
]);

// First call returns vehicle 1
// Second call returns vehicle 2
// Third call returns 404
```

## Request Recording

The fake client records all requests made:

```php
Motive::fake();

Motive::vehicles()->list(['status' => 'active']);
Motive::vehicles()->find(123);
Motive::users()->list();

// Get all recorded requests
$requests = Motive::recorded();

// [
//     ['method' => 'GET', 'path' => '/v1/vehicles', 'query' => ['status' => 'active'], ...],
//     ['method' => 'GET', 'path' => '/v1/vehicles/123', ...],
//     ['method' => 'GET', 'path' => '/v1/users', ...],
// ]
```

## Clearing State

### Clear Fake Responses

```php
$fake = Motive::fake([...]);

// Later in test
$fake->clearFakes();
```

### Clear Recorded Requests

```php
$fake = Motive::fake();

Motive::vehicles()->list();

$fake->clearRecorded();

// Recording is now empty
$this->assertTrue($fake->assertNothingSent());
```

## Testing Different Scenarios

### Success Scenario

```php
public function test_syncs_vehicles_successfully(): void
{
    Motive::fake([
        'vehicles' => Vehicle::factory()->count(5)->make(),
    ]);

    $service = new VehicleSyncService();
    $result = $service->sync();

    $this->assertTrue($result->success);
    $this->assertEquals(5, $result->count);
}
```

### Empty Response Scenario

```php
public function test_handles_no_vehicles(): void
{
    Motive::fake([
        'vehicles' => [],
    ]);

    $service = new VehicleSyncService();
    $result = $service->sync();

    $this->assertTrue($result->success);
    $this->assertEquals(0, $result->count);
}
```

### Error Scenario

```php
public function test_handles_api_error(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::serverError(),
    ]);

    $this->expectException(ServerException::class);

    Motive::vehicles()->list();
}
```

### Authentication Error

```php
public function test_handles_invalid_credentials(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::unauthorized('Invalid API key'),
    ]);

    $this->expectException(AuthenticationException::class);

    Motive::vehicles()->list();
}
```

### Validation Error

```php
public function test_handles_validation_error(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::validationError([
            'number' => ['Vehicle number already exists'],
        ]),
    ]);

    try {
        Motive::vehicles()->create(['number' => 'EXISTING']);
    } catch (ValidationException $e) {
        $this->assertArrayHasKey('number', $e->errors());
    }
}
```

## PHPUnit Setup

### In Each Test

```php
public function test_example(): void
{
    Motive::fake();
    // ...
}
```

### In setUp Method

```php
protected function setUp(): void
{
    parent::setUp();
    Motive::fake();
}
```

### Using a Trait

```php
trait FakesMotiveApi
{
    protected function fakeMotiveApi(array $responses = []): void
    {
        Motive::fake($responses);
    }
}

class VehicleTest extends TestCase
{
    use FakesMotiveApi;

    public function test_example(): void
    {
        $this->fakeMotiveApi([
            'vehicles' => Vehicle::factory()->count(3)->make(),
        ]);

        // ...
    }
}
```

## Integration with Laravel HTTP Fakes

The Motive fake operates independently from Laravel's HTTP fake:

```php
// This fakes Motive API calls
Motive::fake();

// This fakes other HTTP calls (doesn't affect Motive)
Http::fake([
    'external-api.com/*' => Http::response(['data' => 'value']),
]);
```

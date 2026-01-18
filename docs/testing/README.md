# Testing

The Motive SDK provides comprehensive testing utilities to help you write tests for code that interacts with the Motive API without making actual HTTP requests.

## Overview

The testing toolkit includes:

- **Faking** - Replace the Motive client with a fake implementation
- **Factories** - Generate realistic test data for DTOs
- **Assertions** - Verify API interactions in your tests
- **Fake Responses** - Build custom response scenarios

## Quick Start

```php
use Motive\Facades\Motive;
use Motive\Data\Vehicle;

public function test_it_lists_vehicles(): void
{
    // Arrange: Set up fake responses
    Motive::fake([
        'vehicles' => [
            Vehicle::factory()->make(['number' => 'TRUCK-001']),
            Vehicle::factory()->make(['number' => 'TRUCK-002']),
        ],
    ]);

    // Act: Run your code
    $vehicles = Motive::vehicles()->list();

    // Assert: Verify behavior
    Motive::assertSent('vehicles');
    Motive::assertSentCount(1);

    $this->assertCount(2, $vehicles);
}
```

## Testing Workflow

### 1. Fake the Client

Replace the real client with a fake:

```php
// Empty fake - returns empty responses
Motive::fake();

// Fake with specific data
Motive::fake([
    'vehicles' => [Vehicle::factory()->make()],
    'users' => [User::factory()->make()],
]);
```

### 2. Run Your Code

Execute the code that makes API calls:

```php
// Your service/controller/command
$service = new VehicleService();
$result = $service->syncVehicles();
```

### 3. Make Assertions

Verify the expected API interactions:

```php
// Assert requests were made
Motive::assertSent('vehicles');
Motive::assertSentCount(1);

// Assert with conditions
Motive::assertSent('vehicles', function ($request) {
    return $request['query']['status'] === 'active';
});
```

## Example Test Cases

### Testing a Sync Command

```php
public function test_sync_command_imports_vehicles(): void
{
    Motive::fake([
        'vehicles' => Vehicle::factory()->count(3)->make(),
    ]);

    $this->artisan('motive:sync-vehicles')
        ->assertSuccessful();

    Motive::assertSent('vehicles');
    $this->assertDatabaseCount('vehicles', 3);
}
```

### Testing Error Handling

```php
use Motive\Testing\FakeResponse;

public function test_handles_rate_limit(): void
{
    Motive::fake([
        'vehicles' => FakeResponse::rateLimit(60),
    ]);

    $this->expectException(RateLimitException::class);

    Motive::vehicles()->list();
}
```

### Testing Create Operations

```php
public function test_creates_vehicle(): void
{
    Motive::fake([
        'vehicles' => Vehicle::factory()->make(['id' => 123]),
    ]);

    $vehicle = Motive::vehicles()->create([
        'number' => 'NEW-001',
        'vin' => '1HGBH41JXMN109186',
    ]);

    Motive::assertSent('vehicles', function ($request) {
        return $request['data']['number'] === 'NEW-001';
    });

    $this->assertEquals(123, $vehicle->id);
}
```

## Documentation

- [Faking](testing/faking.md) - How to fake the Motive client
- [Factories](testing/factories.md) - Creating test data with factories
- [Assertions](testing/assertions.md) - Available assertion methods
- [Fake Responses](testing/fake-responses.md) - Building custom responses

## Best Practices

1. **Always fake in tests** - Never make real API calls in automated tests
2. **Use factories** - Generate realistic test data
3. **Test error scenarios** - Verify error handling with fake error responses
4. **Assert interactions** - Verify the correct API calls were made
5. **Reset between tests** - Use `Motive::fake()` in setUp or each test

# Assertions

Verify API interactions in your tests using assertion methods.

## Available Assertions

### assertSent

Assert that a request was made to a specific path:

```php
Motive::fake();

Motive::vehicles()->list();

Motive::assertSent('vehicles');
// or with full path
Motive::assertSent('/v1/vehicles');
```

### assertSent with Callback

Assert request details using a callback:

```php
Motive::fake();

Motive::vehicles()->list(['status' => 'active']);

Motive::assertSent('vehicles', function ($request) {
    return $request['query']['status'] === 'active';
});
```

### assertNotSent

Assert that a request was NOT made:

```php
Motive::fake();

Motive::vehicles()->list();

Motive::assertNotSent('users');
Motive::assertNotSent('/v1/dispatches');
```

### assertNothingSent

Assert that no requests were made:

```php
Motive::fake();

// No API calls made
Motive::assertNothingSent();
```

### assertSentCount

Assert the total number of requests:

```php
Motive::fake();

Motive::vehicles()->list();
Motive::users()->list();
Motive::dispatches()->list();

Motive::assertSentCount(3);
```

## Request Structure

The callback receives a request array with these keys:

```php
Motive::assertSent('vehicles', function (array $request) {
    // $request['method']    - HTTP method (GET, POST, etc.)
    // $request['path']      - Request path
    // $request['query']     - Query parameters
    // $request['data']      - Request body data
    // $request['timestamp'] - When request was made

    return true;
});
```

## Asserting Request Data

### Query Parameters

```php
Motive::fake();

Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);

Motive::assertSent('vehicles', function ($request) {
    return $request['query']['status'] === 'active'
        && $request['query']['per_page'] === 50;
});
```

### POST/PUT Data

```php
Motive::fake();

Motive::vehicles()->create([
    'number' => 'TRUCK-001',
    'vin' => '1HGBH41JXMN109186',
]);

Motive::assertSent('vehicles', function ($request) {
    return $request['method'] === 'POST'
        && $request['data']['number'] === 'TRUCK-001';
});
```

### HTTP Method

```php
Motive::fake();

Motive::vehicles()->update(123, ['number' => 'NEW-001']);

Motive::assertSent('vehicles/123', function ($request) {
    return $request['method'] === 'PATCH';
});
```

## Testing Specific Operations

### List Operation

```php
public function test_lists_active_vehicles(): void
{
    Motive::fake([
        'vehicles' => Vehicle::factory()->count(5)->make(),
    ]);

    $service = new VehicleService();
    $vehicles = $service->getActiveVehicles();

    Motive::assertSent('vehicles', function ($request) {
        return $request['method'] === 'GET'
            && ($request['query']['status'] ?? null) === 'active';
    });

    $this->assertCount(5, $vehicles);
}
```

### Find Operation

```php
public function test_finds_vehicle_by_id(): void
{
    Motive::fake([
        'vehicles/123' => Vehicle::factory()->make(['id' => 123]),
    ]);

    $vehicle = Motive::vehicles()->find(123);

    Motive::assertSent('vehicles/123');
    $this->assertEquals(123, $vehicle->id);
}
```

### Create Operation

```php
public function test_creates_vehicle(): void
{
    Motive::fake([
        'vehicles' => Vehicle::factory()->make(['id' => 999]),
    ]);

    $vehicle = Motive::vehicles()->create([
        'number' => 'NEW-001',
        'vin' => '1HGBH41JXMN109186',
    ]);

    Motive::assertSent('vehicles', function ($request) {
        return $request['method'] === 'POST'
            && $request['data']['number'] === 'NEW-001'
            && $request['data']['vin'] === '1HGBH41JXMN109186';
    });
}
```

### Update Operation

```php
public function test_updates_vehicle(): void
{
    Motive::fake([
        'vehicles/*' => Vehicle::factory()->make(),
    ]);

    Motive::vehicles()->update(123, [
        'number' => 'UPDATED-001',
    ]);

    Motive::assertSent('vehicles/123', function ($request) {
        return $request['method'] === 'PATCH'
            && $request['data']['number'] === 'UPDATED-001';
    });
}
```

### Delete Operation

```php
public function test_deletes_vehicle(): void
{
    Motive::fake();

    Motive::vehicles()->delete(123);

    Motive::assertSent('vehicles/123', function ($request) {
        return $request['method'] === 'DELETE';
    });
}
```

## Accessing Recorded Requests

### Get All Requests

```php
Motive::fake();

Motive::vehicles()->list();
Motive::users()->list();

$requests = Motive::recorded();

$this->assertCount(2, $requests);
$this->assertEquals('/v1/vehicles', $requests[0]['path']);
$this->assertEquals('/v1/users', $requests[1]['path']);
```

### Filter by Path

```php
$fake = Motive::fake();

Motive::vehicles()->list();
Motive::vehicles()->find(123);
Motive::users()->list();

$vehicleRequests = $fake->history->forPath('vehicles');

$this->assertCount(2, $vehicleRequests);
```

### Filter by Method

```php
$fake = Motive::fake();

Motive::vehicles()->list();
Motive::vehicles()->create([...]);

$getRequests = $fake->history->forMethod('GET');
$postRequests = $fake->history->forMethod('POST');

$this->assertCount(1, $getRequests);
$this->assertCount(1, $postRequests);
```

## Complex Assertions

### Multiple Conditions

```php
Motive::assertSent('vehicles', function ($request) {
    return $request['method'] === 'GET'
        && isset($request['query']['status'])
        && isset($request['query']['per_page'])
        && $request['query']['per_page'] <= 100;
});
```

### Any of Multiple Requests

```php
Motive::fake();

// Code that may make multiple requests
$service->syncAll();

// Assert at least one vehicles request was made
$this->assertTrue(
    Motive::assertSent('vehicles')
);
```

### Ordered Assertions

```php
Motive::fake();

$service->syncData();

$requests = Motive::recorded();

// Verify order of operations
$this->assertEquals('/v1/vehicles', $requests[0]['path']);
$this->assertEquals('/v1/users', $requests[1]['path']);
$this->assertEquals('/v1/dispatches', $requests[2]['path']);
```

## PHPUnit Integration

### Custom Assertion Methods

```php
trait MotiveAssertions
{
    protected function assertMotiveSent(string $path, ?callable $callback = null): void
    {
        $this->assertTrue(
            Motive::assertSent($path, $callback),
            "Failed asserting that request was sent to [{$path}]"
        );
    }

    protected function assertMotiveNotSent(string $path): void
    {
        $this->assertTrue(
            Motive::assertNotSent($path),
            "Failed asserting that request was NOT sent to [{$path}]"
        );
    }
}
```

### Usage

```php
class VehicleTest extends TestCase
{
    use MotiveAssertions;

    public function test_example(): void
    {
        Motive::fake();

        // ... your code

        $this->assertMotiveSent('vehicles');
        $this->assertMotiveNotSent('users');
    }
}
```

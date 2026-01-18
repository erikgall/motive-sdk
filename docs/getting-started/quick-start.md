# Quick Start

This guide will help you make your first API calls with the Motive SDK.

## Using the Facade

The simplest way to use the SDK is through the `Motive` facade:

```php
use Motive\Facades\Motive;

// List all vehicles
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}
```

## Using Dependency Injection

For better testability, inject the `MotiveManager` class:

```php
use Motive\MotiveManager;

class VehicleController extends Controller
{
    public function __construct(
        protected MotiveManager $motive
    ) {}

    public function index()
    {
        return $this->motive->vehicles()->list();
    }
}
```

## Common Operations

### Listing Resources

```php
// List all vehicles with lazy pagination
$vehicles = Motive::vehicles()->list();

// List with filters
$activeVehicles = Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);
```

### Finding a Resource

```php
// Find by ID
$vehicle = Motive::vehicles()->find(123);

// Find by external identifier
$vehicle = Motive::vehicles()->findByNumber('TRUCK-001');
```

### Creating a Resource

```php
$vehicle = Motive::vehicles()->create([
    'number' => 'TRUCK-042',
    'make' => 'Freightliner',
    'model' => 'Cascadia',
    'year' => 2024,
    'vin' => '1FUJGLDR5CLBP8834',
]);

echo "Created vehicle #{$vehicle->id}";
```

### Updating a Resource

```php
$vehicle = Motive::vehicles()->update(123, [
    'number' => 'TRUCK-042-UPDATED',
    'license_plate_number' => 'XYZ9876',
]);
```

### Deleting a Resource

```php
$deleted = Motive::vehicles()->delete(123);

if ($deleted) {
    echo "Vehicle deleted successfully";
}
```

## Working with DTOs

All responses are automatically converted to strongly-typed Data Transfer Objects:

```php
$vehicle = Motive::vehicles()->find(123);

// Properties are typed
echo $vehicle->id;          // int
echo $vehicle->number;      // string
echo $vehicle->make;        // string
echo $vehicle->status;      // VehicleStatus enum
echo $vehicle->createdAt;   // CarbonImmutable
```

## Handling Pagination

### Lazy Pagination (Recommended)

Lazy pagination fetches pages on-demand, making it memory-efficient for large datasets:

```php
// Automatically fetches pages as you iterate
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    // Each page is fetched only when needed
    echo $vehicle->number;
}
```

### Standard Pagination

For more control over pagination:

```php
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);

echo "Showing {$page->count()} of {$page->total()} vehicles";
echo "Page {$page->currentPage()} of {$page->lastPage()}";

foreach ($page->items() as $vehicle) {
    echo $vehicle->number;
}

// Check for more pages
if ($page->hasMorePages()) {
    $nextPage = Motive::vehicles()->paginate(page: 2);
}
```

## Error Handling

Wrap API calls in try-catch blocks to handle errors:

```php
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\ValidationException;
use Motive\Exceptions\MotiveException;

try {
    $vehicle = Motive::vehicles()->find(123);
} catch (NotFoundException $e) {
    // Vehicle not found (404)
    echo "Vehicle not found";
} catch (ValidationException $e) {
    // Invalid request data (422)
    foreach ($e->errors() as $field => $messages) {
        echo "{$field}: " . implode(', ', $messages);
    }
} catch (MotiveException $e) {
    // Any other API error
    echo "Error: " . $e->getMessage();
}
```

## Example: Fleet Dashboard

Here's a complete example building a simple fleet dashboard:

```php
use Motive\Facades\Motive;

class FleetDashboardController extends Controller
{
    public function index()
    {
        // Get all active vehicles
        $vehicles = Motive::vehicles()->list(['status' => 'active']);

        // Get drivers with HOS availability
        $drivers = [];
        foreach (Motive::users()->list(['role' => 'driver']) as $user) {
            $availability = Motive::hosAvailability()->forDriver($user->id);
            $drivers[] = [
                'user' => $user,
                'availability' => $availability,
            ];
        }

        // Get recent HOS violations
        $violations = Motive::hosViolations()->list([
            'start_date' => now()->subDays(7)->toDateString(),
        ]);

        return view('fleet.dashboard', compact('vehicles', 'drivers', 'violations'));
    }
}
```

## Next Steps

- [Understand the SDK architecture](../core-concepts/architecture.md)
- [Learn about all available resources](../api-reference/README.md)
- [Set up webhooks](../webhooks/README.md)
- [Write tests with faking](../testing/faking.md)

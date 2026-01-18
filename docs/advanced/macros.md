# Macros

Extend SDK resources with custom methods using Laravel's Macroable trait.

## Overview

All resource classes are macroable, allowing you to add custom methods without modifying the SDK source code. This is useful for adding domain-specific functionality.

## Adding Macros

### Basic Macro

```php
use Motive\Resources\Vehicles\VehiclesResource;

// In a service provider
public function boot(): void
{
    VehiclesResource::macro('findByLicensePlate', function (string $plate) {
        return $this->list(['license_plate_number' => $plate])->first();
    });
}
```

### Usage

```php
use Motive\Facades\Motive;

$vehicle = Motive::vehicles()->findByLicensePlate('ABC1234');
```

## Common Patterns

### Custom Finders

```php
use Motive\Resources\Users\UsersResource;

UsersResource::macro('findByEmail', function (string $email) {
    return $this->list(['email' => $email])->first();
});

UsersResource::macro('findByPhone', function (string $phone) {
    return $this->list(['phone' => $phone])->first();
});

// Usage
$user = Motive::users()->findByEmail('john@example.com');
$user = Motive::users()->findByPhone('555-1234');
```

### Filtered Lists

```php
use Motive\Resources\Vehicles\VehiclesResource;

VehiclesResource::macro('active', function () {
    return $this->list(['status' => 'active']);
});

VehiclesResource::macro('inactive', function () {
    return $this->list(['status' => 'inactive']);
});

VehiclesResource::macro('byMake', function (string $make) {
    return $this->list(['make' => $make]);
});

// Usage
$activeVehicles = Motive::vehicles()->active();
$freightliners = Motive::vehicles()->byMake('Freightliner');
```

### Aggregations

```php
use Motive\Resources\Vehicles\VehiclesResource;

VehiclesResource::macro('countByStatus', function () {
    $vehicles = $this->list();

    return $vehicles->groupBy(fn ($v) => $v->status->value)
        ->map(fn ($group) => $group->count());
});

// Usage
$counts = Motive::vehicles()->countByStatus();
// ['active' => 45, 'inactive' => 12]
```

### Domain-Specific Methods

```php
use Motive\Resources\Dispatches\DispatchesResource;

DispatchesResource::macro('pendingForDriver', function (int $driverId) {
    return $this->list([
        'driver_id' => $driverId,
        'status' => 'pending',
    ]);
});

DispatchesResource::macro('completedToday', function () {
    return $this->list([
        'status' => 'completed',
        'completed_after' => now()->startOfDay()->toIso8601String(),
    ]);
});

// Usage
$pending = Motive::dispatches()->pendingForDriver(123);
$todayComplete = Motive::dispatches()->completedToday();
```

## Macros with Parameters

### Flexible Parameters

```php
use Motive\Resources\Compliance\HosLogsResource;

HosLogsResource::macro('forDriverInRange', function (int $driverId, $startDate, $endDate) {
    return $this->list([
        'driver_ids' => [$driverId],
        'start_date' => $startDate->toIso8601String(),
        'end_date' => $endDate->toIso8601String(),
    ]);
});

// Usage
$logs = Motive::hosLogs()->forDriverInRange(
    123,
    now()->subWeek(),
    now()
);
```

### Optional Parameters

```php
VehiclesResource::macro('search', function (array $criteria = []) {
    $params = array_filter([
        'status' => $criteria['status'] ?? null,
        'make' => $criteria['make'] ?? null,
        'year' => $criteria['year'] ?? null,
    ]);

    return $this->list($params);
});

// Usage
$results = Motive::vehicles()->search([
    'make' => 'Peterbilt',
    'status' => 'active',
]);
```

## Chained Operations

```php
VehiclesResource::macro('activeWithLocation', function () {
    return $this->active()
        ->filter(fn ($v) => $v->latitude !== null);
});

// Usage
$trackedVehicles = Motive::vehicles()->activeWithLocation();
```

## Registering Multiple Macros

### Using a Dedicated Class

```php
// app/Support/MotiveMacros.php
namespace App\Support;

use Motive\Resources\Vehicles\VehiclesResource;
use Motive\Resources\Users\UsersResource;
use Motive\Resources\Dispatches\DispatchesResource;

class MotiveMacros
{
    public static function register(): void
    {
        self::registerVehicleMacros();
        self::registerUserMacros();
        self::registerDispatchMacros();
    }

    protected static function registerVehicleMacros(): void
    {
        VehiclesResource::macro('findByVin', function (string $vin) {
            return $this->list(['vin' => $vin])->first();
        });

        VehiclesResource::macro('needingMaintenance', function () {
            // Custom logic
        });
    }

    protected static function registerUserMacros(): void
    {
        UsersResource::macro('drivers', function () {
            return $this->list(['role' => 'driver']);
        });

        UsersResource::macro('admins', function () {
            return $this->list(['role' => 'admin']);
        });
    }

    protected static function registerDispatchMacros(): void
    {
        // ...
    }
}
```

### In Service Provider

```php
// app/Providers/AppServiceProvider.php
use App\Support\MotiveMacros;

public function boot(): void
{
    MotiveMacros::register();
}
```

## Testing Macros

```php
use Motive\Facades\Motive;
use Motive\Data\Vehicle;
use Motive\Resources\Vehicles\VehiclesResource;

public function test_find_by_license_plate_macro(): void
{
    VehiclesResource::macro('findByLicensePlate', function (string $plate) {
        return $this->list(['license_plate_number' => $plate])->first();
    });

    Motive::fake([
        'vehicles' => [
            Vehicle::factory()->make(['license_plate_number' => 'ABC1234']),
        ],
    ]);

    $vehicle = Motive::vehicles()->findByLicensePlate('ABC1234');

    $this->assertNotNull($vehicle);
    $this->assertEquals('ABC1234', $vehicle->licensePlateNumber);
}
```

## Best Practices

1. **Register in boot()** - Always register macros in service provider's `boot()` method
2. **Document macros** - Add PHPDoc comments to your macro registration
3. **Test macros** - Write tests for custom functionality
4. **Keep focused** - Each macro should do one thing well
5. **Use type hints** - Parameter and return type hints improve IDE support

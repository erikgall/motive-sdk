# Factories

Generate realistic test data using factories for all DTO classes.

## Overview

Factories provide a convenient way to create DTO instances with realistic default values. They support customization, states, sequences, and batch creation.

## Basic Usage

### Creating a Single Instance

```php
use Motive\Data\Vehicle;

// Create with defaults
$vehicle = Vehicle::factory()->make();

// Create with custom attributes
$vehicle = Vehicle::factory()->make([
    'number' => 'CUSTOM-001',
    'status' => 'active',
]);
```

### Creating Multiple Instances

```php
// Create array of instances
$vehicles = Vehicle::factory()->count(5)->make();

// With custom attributes
$vehicles = Vehicle::factory()->count(3)->make([
    'status' => 'active',
]);
```

### Getting Raw Array Data

```php
// Get array instead of DTO
$data = Vehicle::factory()->raw();

// Multiple arrays
$data = Vehicle::factory()->count(5)->raw();
```

## Available Factories

| DTO | Factory |
|-----|---------|
| `Vehicle` | `Vehicle::factory()` |
| `User` | `User::factory()` |
| `Asset` | `Asset::factory()` |
| `HosLog` | `HosLog::factory()` |
| `HosAvailability` | `HosAvailability::factory()` |
| `Dispatch` | `Dispatch::factory()` |
| `Location` | `Location::factory()` |
| `Geofence` | `Geofence::factory()` |
| `Message` | `Message::factory()` |
| `Document` | `Document::factory()` |
| `InspectionReport` | `InspectionReport::factory()` |
| `FuelPurchase` | `FuelPurchase::factory()` |
| `Webhook` | `Webhook::factory()` |

## Factory States

### Using Predefined States

Factories include common states:

```php
// Vehicle states
$vehicle = Vehicle::factory()->inactive()->make();
$vehicle = Vehicle::factory()->withVin('1HGBH41JXMN109186')->make();

// User states
$user = User::factory()->admin()->make();
$user = User::factory()->driver()->make();
$user = User::factory()->deactivated()->make();
```

### Custom States

Apply custom state modifications:

```php
$vehicle = Vehicle::factory()
    ->state([
        'status' => 'maintenance',
        'make' => 'Peterbilt',
        'year' => 2024,
    ])
    ->make();
```

### Chaining States

```php
$user = User::factory()
    ->driver()
    ->state(['company_id' => 42])
    ->make();
```

## Sequences

### Sequential Values

Create instances with varying attribute values:

```php
$vehicles = Vehicle::factory()
    ->count(3)
    ->sequence(
        ['status' => 'active'],
        ['status' => 'inactive'],
        ['status' => 'maintenance']
    )
    ->make();

// First vehicle: active
// Second vehicle: inactive
// Third vehicle: maintenance
```

### Cycling Sequences

Sequences cycle when count exceeds sequence length:

```php
$vehicles = Vehicle::factory()
    ->count(6)
    ->sequence(
        ['make' => 'Freightliner'],
        ['make' => 'Peterbilt']
    )
    ->make();

// Results: Freightliner, Peterbilt, Freightliner, Peterbilt, ...
```

## Factory Definitions

### VehicleFactory

```php
// Default definition
[
    'id' => $autoIncrement,
    'company_id' => 1,
    'number' => 'V-0001',
    'make' => 'Freightliner', // Random from list
    'model' => 'Cascadia',    // Random from list
    'year' => 2018-2024,      // Random in range
    'vin' => 'ABC123...',     // Random VIN
    'status' => 'active',
]
```

### UserFactory

```php
// Default definition
[
    'id' => $autoIncrement,
    'company_id' => 1,
    'first_name' => 'John',   // Random from list
    'last_name' => 'Smith',   // Random from list
    'email' => 'john.smith1@example.com',
    'phone' => '555-1234',
    'role' => 'driver',
    'status' => 'active',
]
```

## Using Factories in Tests

### Faking API Responses

```php
use Motive\Facades\Motive;
use Motive\Data\Vehicle;

public function test_lists_vehicles(): void
{
    Motive::fake([
        'vehicles' => Vehicle::factory()->count(3)->make(),
    ]);

    $vehicles = Motive::vehicles()->list();

    $this->assertCount(3, $vehicles);
}
```

### Testing with Specific Data

```php
public function test_finds_vehicle_by_number(): void
{
    Motive::fake([
        'vehicles' => [
            Vehicle::factory()->make(['number' => 'TRUCK-001']),
            Vehicle::factory()->make(['number' => 'TRUCK-002']),
        ],
    ]);

    // Your code that searches for 'TRUCK-001'
}
```

### Testing Relationships

```php
public function test_assigns_driver_to_vehicle(): void
{
    $driver = User::factory()->driver()->make(['id' => 456]);
    $vehicle = Vehicle::factory()->make([
        'id' => 123,
        'current_driver_id' => 456,
    ]);

    Motive::fake([
        'users' => [$driver],
        'vehicles' => [$vehicle],
    ]);

    // Test your logic
}
```

## Creating Custom Factories

### Extend the Base Factory

```php
namespace Tests\Factories;

use Motive\Testing\Factories\Factory;
use App\DTOs\CustomDto;

class CustomDtoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->generateId(),
            'name' => 'Custom Item ' . $this->generateId(),
            'status' => 'active',
        ];
    }

    public function dtoClass(): string
    {
        return CustomDto::class;
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
```

### Usage

```php
$dto = CustomDtoFactory::new()->make();
$dto = CustomDtoFactory::new()->inactive()->make();
```

## Advanced Patterns

### Factory Callbacks

```php
$vehicles = Vehicle::factory()
    ->count(5)
    ->make()
    ->map(function ($vehicle, $index) {
        // Post-process if needed
        return $vehicle;
    });
```

### Conditional Factories

```php
$vehicles = collect(range(1, 10))->map(function ($i) {
    return Vehicle::factory()->make([
        'status' => $i % 2 === 0 ? 'active' : 'inactive',
    ]);
});
```

### Factory in Database Seeders

```php
// For integration tests that need database records
foreach (Vehicle::factory()->count(10)->raw() as $data) {
    DB::table('vehicles')->insert([
        'motive_id' => $data['id'],
        'number' => $data['number'],
        'vin' => $data['vin'],
    ]);
}
```

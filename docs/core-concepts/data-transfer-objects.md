# Data Transfer Objects

Data Transfer Objects (DTOs) are immutable objects that represent API response data with strong typing and automatic value casting.

## Overview

Every API response is converted to a DTO:

```php
$vehicle = Motive::vehicles()->find(123);

// $vehicle is a Vehicle DTO
echo $vehicle->id;        // int
echo $vehicle->number;    // string
echo $vehicle->status;    // VehicleStatus enum
echo $vehicle->createdAt; // CarbonImmutable
```

## Base Class

All DTOs extend the `DataTransferObject` base class, which itself extends Laravel's `Fluent` class:

```php
namespace Motive\Data;

abstract class DataTransferObject extends Fluent
{
    protected array $casts = [];
    protected array $defaults = [];
    protected array $mappings = [];
    protected array $nestedArrays = [];
}
```

## Type Casting

DTOs automatically cast values to appropriate types:

### Primitive Types

```php
protected array $casts = [
    'id' => 'int',
    'name' => 'string',
    'speed' => 'float',
    'active' => 'bool',
];
```

### Enums

```php
use Motive\Enums\VehicleStatus;

protected array $casts = [
    'status' => VehicleStatus::class,
];

// Access returns the enum
$vehicle->status === VehicleStatus::Active; // true
```

### Dates

```php
use Carbon\CarbonImmutable;

protected array $casts = [
    'createdAt' => CarbonImmutable::class,
    'updatedAt' => 'datetime',
];

// Access returns CarbonImmutable
$vehicle->createdAt->format('Y-m-d'); // "2024-01-15"
```

### Nested DTOs

```php
use Motive\Data\Driver;

protected array $casts = [
    'driver' => Driver::class,
];

// Access returns a Driver DTO
$user->driver->licenseNumber; // "DL123456"
```

### Arrays of DTOs

```php
use Motive\Data\DispatchStop;

protected array $nestedArrays = [
    'stops' => DispatchStop::class,
];

// Access returns array of DispatchStop DTOs
foreach ($dispatch->stops as $stop) {
    echo $stop->address;
}
```

## Property Access

DTOs support multiple access patterns:

```php
$vehicle = Motive::vehicles()->find(123);

// Property access (recommended)
$vehicle->number;

// Array access
$vehicle['number'];

// Method access
$vehicle->get('number');
$vehicle->get('number', 'default');
```

## Available DTOs

The SDK includes 50 DTOs organized by domain:

### Fleet DTOs
- `Vehicle` - Fleet vehicles
- `Asset` - Trailers and equipment
- `VehicleGateway` - ELD device information
- `VehicleLocation` - Vehicle position data
- `FaultCode` - Diagnostic trouble codes

### Driver DTOs
- `User` - Driver and staff data
- `Driver` - Driver-specific information
- `DrivingPeriod` - Driving activity periods
- `Scorecard` - Safety scores

### Compliance DTOs
- `HosLog` - Hours of Service entries
- `HosAvailability` - Remaining drive time
- `HosViolation` - HOS rule violations
- `InspectionReport` - DVIR reports
- `InspectionDefect` - Reported defects
- `IftaReport` - Fuel tax reports
- `IftaJurisdiction` - Per-state IFTA data

### Dispatch DTOs
- `Dispatch` - Load/route assignments
- `DispatchStop` - Pickup/delivery stops
- `Location` - Named locations
- `Geofence` - Geographic boundaries
- `GeofenceCoordinate` - Polygon vertices

### Communication DTOs
- `Message` - Driver messages
- `Document` - Document records
- `DocumentImage` - Document image data

### Safety DTOs
- `DriverPerformanceEvent` - Safety events
- `CameraConnection` - Camera device info
- `Video` - Video recording data
- `VideoRequest` - Video request status

### Operations DTOs
- `Group` - Organizational groups
- `GroupMember` - Group membership
- `Company` - Company information
- `Form` - Custom form templates
- `FormField` - Form field definitions
- `FormEntry` - Submitted form data
- `Timecard` - Time tracking records
- `TimecardEntry` - Individual time entries
- `UtilizationReport` - Utilization metrics
- `UtilizationDay` - Daily utilization

### Financial DTOs
- `FuelPurchase` - Fuel transactions
- `MotiveCard` - Fuel card data
- `CardTransaction` - Card transactions
- `CardLimit` - Card spending limits

### Integration DTOs
- `Webhook` - Webhook subscriptions
- `WebhookLog` - Webhook delivery logs
- `ExternalId` - External ID mappings
- `Shipment` - Freight shipment data
- `ShipmentTracking` - Shipment position
- `ShipmentEta` - Estimated arrival
- `ReeferActivity` - Reefer temperature data

## Creating DTOs

While DTOs are typically created by the SDK from API responses, you can create them manually:

```php
use Motive\Data\Vehicle;

// From array
$vehicle = Vehicle::from([
    'id' => 123,
    'number' => 'TRUCK-001',
    'status' => 'active',
]);

// Via constructor
$vehicle = new Vehicle([
    'id' => 123,
    'number' => 'TRUCK-001',
]);
```

## Serialization

DTOs can be converted to arrays or JSON:

```php
$vehicle = Motive::vehicles()->find(123);

// To array (snake_case keys)
$array = $vehicle->toArray();
// ['id' => 123, 'number' => 'TRUCK-001', 'created_at' => '2024-01-15T...']

// To JSON
$json = json_encode($vehicle);
```

## Property Documentation

DTOs are documented with `@property` PHPDoc tags for IDE support:

```php
/**
 * Vehicle data transfer object.
 *
 * @property int $id
 * @property string $number
 * @property string|null $make
 * @property string|null $model
 * @property int|null $year
 * @property string|null $vin
 * @property VehicleStatus $status
 * @property CarbonImmutable|null $createdAt
 */
class Vehicle extends DataTransferObject
```

This provides autocomplete and type checking in your IDE.

## Defaults

DTOs can specify default values for properties:

```php
protected array $defaults = [
    'active' => true,
    'status' => 'pending',
];
```

## Property Mappings

DTOs can map API response keys to different property names:

```php
protected array $mappings = [
    'driver_user' => 'driver',
    'current_vehicle' => 'vehicle',
];
```

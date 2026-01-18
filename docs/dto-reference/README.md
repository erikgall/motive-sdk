# DTO Reference

Data Transfer Objects (DTOs) are immutable objects that represent API response data. This section provides detailed documentation for all 50 DTOs.

## Overview

DTOs provide:
- **Strong typing** - All properties have defined types
- **Automatic casting** - Values are automatically converted to appropriate types
- **IDE support** - Full autocomplete and type hints via PHPDoc
- **Serialization** - Convert to arrays or JSON

## DTO Categories

### [Fleet DTOs](fleet.md)
- `Vehicle` - Fleet vehicles
- `Asset` - Trailers and equipment
- `VehicleGateway` - ELD device information
- `VehicleLocation` - Vehicle position data
- `FaultCode` - Diagnostic trouble codes

### [Driver DTOs](drivers.md)
- `User` - Driver and staff data
- `Driver` - Driver-specific information
- `DrivingPeriod` - Driving activity periods
- `Scorecard` - Safety scores

### [Compliance DTOs](compliance.md)
- `HosLog` - Hours of Service entries
- `HosAvailability` - Remaining drive time
- `HosViolation` - HOS rule violations
- `InspectionReport` - DVIR reports
- `InspectionDefect` - Reported defects
- `IftaReport` - Fuel tax reports
- `IftaJurisdiction` - Per-state IFTA data

### [Dispatch DTOs](dispatch.md)
- `Dispatch` - Load/route assignments
- `DispatchStop` - Pickup/delivery stops
- `Location` - Named locations
- `Geofence` - Geographic boundaries
- `GeofenceCoordinate` - Polygon vertices

### [Communication DTOs](communication.md)
- `Message` - Driver messages
- `Document` - Document records
- `DocumentImage` - Document image data

### [Safety DTOs](safety.md)
- `DriverPerformanceEvent` - Safety events
- `CameraConnection` - Camera device info
- `Video` - Video recording data
- `VideoRequest` - Video request status

### [Operations DTOs](operations.md)
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

### [Financial DTOs](financial.md)
- `FuelPurchase` - Fuel transactions
- `MotiveCard` - Fuel card data
- `CardTransaction` - Card transactions
- `CardLimit` - Card spending limits

### [Integration DTOs](integration.md)
- `Webhook` - Webhook subscriptions
- `WebhookLog` - Webhook delivery logs
- `ExternalId` - External ID mappings
- `Shipment` - Freight shipment data
- `ShipmentTracking` - Shipment position
- `ShipmentEta` - Estimated arrival
- `ReeferActivity` - Reefer temperature data

## Working with DTOs

### Property Access

```php
$vehicle = Motive::vehicles()->find(123);

// Property access
echo $vehicle->number;

// Array access
echo $vehicle['number'];

// Method access with default
echo $vehicle->get('number', 'N/A');
```

### Type Casting

DTOs automatically cast values:

```php
$vehicle->id;        // int
$vehicle->number;    // string
$vehicle->status;    // VehicleStatus enum
$vehicle->createdAt; // CarbonImmutable
```

### Serialization

```php
// To array (snake_case keys)
$array = $vehicle->toArray();

// To JSON
$json = json_encode($vehicle);
```

### Creating DTOs

```php
use Motive\Data\Vehicle;

// From array
$vehicle = Vehicle::from([
    'id' => 123,
    'number' => 'TRUCK-001',
    'status' => 'active',
]);
```

## Base Class

All DTOs extend `Motive\Data\DataTransferObject`:

```php
abstract class DataTransferObject extends Fluent
{
    protected array $casts = [];
    protected array $defaults = [];
    protected array $mappings = [];
    protected array $nestedArrays = [];
}
```

## Cast Types

| Cast | Description |
|------|-------------|
| `'int'`, `'integer'` | Cast to integer |
| `'float'`, `'double'` | Cast to float |
| `'string'` | Cast to string |
| `'bool'`, `'boolean'` | Cast to boolean |
| `'array'` | Cast to array |
| `CarbonImmutable::class` | Parse as datetime |
| `SomeEnum::class` | Cast to enum |
| `SomeDto::class` | Cast to nested DTO |

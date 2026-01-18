# Enum Reference

The Motive SDK uses PHP 8.1+ backed enums to provide type safety for status and type values. This section documents all 26 enums.

## Overview

Enums provide:
- **Type safety** - Prevent invalid values at compile time
- **IDE support** - Autocomplete and refactoring support
- **Consistent values** - All valid options in one place
- **Self-documenting** - Clear mapping between code and API values

## Enum Categories

### [Status Enums](status-enums.md)
Represent the state or status of a resource.
- `VehicleStatus`
- `AssetStatus`
- `UserStatus`
- `DispatchStatus`
- `DocumentStatus`
- `InspectionStatus`
- `WebhookStatus`
- `TimecardStatus`
- `VideoStatus`
- `ShipmentStatus`

### [Type Enums](type-enums.md)
Classify or categorize resources.
- `AssetType`
- `GeofenceType`
- `StopType`
- `DocumentType`
- `InspectionType`
- `PerformanceEventType`
- `FormFieldType`
- `CardTransactionType`
- `CameraType`

### [HOS Enums](hos-enums.md)
Hours of Service specific enums.
- `DutyStatus`
- `HosViolationType`

### [Scope Enums](scope-enums.md)
OAuth permission scopes.
- `Scope`

### [Webhook Enums](webhook-enums.md)
Webhook related enums.
- `WebhookEvent`
- `MessageDirection`
- `EventSeverity`
- `UserRole`

## Working with Enums

### Comparison

```php
use Motive\Enums\VehicleStatus;

$vehicle = Motive::vehicles()->find(123);

if ($vehicle->status === VehicleStatus::Active) {
    echo "Vehicle is active";
}
```

### Getting Values

```php
use Motive\Enums\DutyStatus;

// Get the string value
$value = DutyStatus::Driving->value;  // "driving"

// Get the name
$name = DutyStatus::Driving->name;    // "Driving"
```

### In API Requests

```php
use Motive\Enums\DutyStatus;

$logs = Motive::hosLogs()->list([
    'duty_status' => DutyStatus::Driving->value,
]);
```

### From API Responses

DTOs automatically cast to enums:

```php
$vehicle = Motive::vehicles()->find(123);

// Already cast to enum
$vehicle->status;  // VehicleStatus::Active

// Compare directly
$vehicle->status === VehicleStatus::Active;  // true
```

### Listing All Values

```php
use Motive\Enums\VehicleStatus;

foreach (VehicleStatus::cases() as $status) {
    echo "{$status->name}: {$status->value}\n";
}
```

### Match Expressions

```php
use Motive\Enums\DutyStatus;

$label = match ($log->status) {
    DutyStatus::Driving => 'On the Road',
    DutyStatus::OnDuty => 'Working',
    DutyStatus::OffDuty => 'Off Shift',
    DutyStatus::SleeperBerth => 'Resting',
    default => 'Unknown',
};
```

## Backed Enum Pattern

All SDK enums are backed string enums:

```php
enum VehicleStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
```

This ensures:
- Values match API expectations
- Easy serialization to/from JSON
- Type-safe comparisons

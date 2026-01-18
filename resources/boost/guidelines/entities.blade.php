## Motive SDK Data Transfer Objects (DTOs)

The SDK uses type-safe DTOs for all API responses. Each DTO extends `DataTransferObject` which is built on Laravel's Fluent class.

### Core Entities

#### Vehicle

@verbatim
<code-snippet name="Vehicle DTO" lang="php">
use Motive\Data\Vehicle;
use Motive\Enums\VehicleStatus;

$vehicle = Motive::vehicles()->find(123);

// Properties (all typed)
$vehicle->id;              // int
$vehicle->companyId;       // int
$vehicle->number;          // ?string
$vehicle->make;            // ?string
$vehicle->model;           // ?string
$vehicle->year;            // ?int
$vehicle->vin;             // ?string
$vehicle->licensePlate;    // ?string
$vehicle->status;          // VehicleStatus enum
$vehicle->currentDriver;   // ?Driver (nested DTO)
$vehicle->createdAt;       // ?CarbonImmutable
$vehicle->updatedAt;       // ?CarbonImmutable
</code-snippet>
@endverbatim

#### VehicleLocation

@verbatim
<code-snippet name="VehicleLocation DTO" lang="php">
use Motive\Data\VehicleLocation;

$location = Motive::vehicles()->currentLocation(123);

$location->latitude;   // float
$location->longitude;  // float
$location->speed;      // ?float
$location->bearing;    // ?int
$location->address;    // ?string
$location->locatedAt;  // ?CarbonImmutable
</code-snippet>
@endverbatim

#### User

@verbatim
<code-snippet name="User DTO" lang="php">
use Motive\Data\User;
use Motive\Enums\UserRole;
use Motive\Enums\UserStatus;

$user = Motive::users()->find(456);

$user->id;          // int
$user->email;       // string
$user->firstName;   // string
$user->lastName;    // string
$user->role;        // UserRole enum
$user->status;      // UserStatus enum
$user->driver;      // ?Driver (nested DTO)
$user->createdAt;   // ?CarbonImmutable
</code-snippet>
@endverbatim

#### Driver

@verbatim
<code-snippet name="Driver DTO" lang="php">
use Motive\Data\Driver;

$driver = $user->driver;

$driver->id;                    // int
$driver->firstName;             // string
$driver->lastName;              // string
$driver->licenseNumber;         // ?string
$driver->licenseState;          // ?string
$driver->licenseExpiration;     // ?CarbonImmutable
$driver->carrier;               // ?string
$driver->currentVehicleId;      // ?int
</code-snippet>
@endverbatim

### HOS & Compliance Entities

#### HosLog

@verbatim
<code-snippet name="HosLog DTO" lang="php">
use Motive\Data\HosLog;
use Motive\Enums\DutyStatus;

$log->id;            // int
$log->driverId;      // int
$log->vehicleId;     // ?int
$log->dutyStatus;    // DutyStatus enum
$log->startTime;     // CarbonImmutable
$log->endTime;       // ?CarbonImmutable
$log->duration;      // ?int (minutes)
$log->location;      // ?string
$log->annotation;    // ?string
$log->certified;     // bool
</code-snippet>
@endverbatim

#### HosAvailability

@verbatim
<code-snippet name="HosAvailability DTO" lang="php">
use Motive\Data\HosAvailability;

$availability->driverId;            // int
$availability->driveTimeRemaining;  // int (minutes)
$availability->shiftTimeRemaining;  // int (minutes)
$availability->cycleTimeRemaining;  // int (minutes)
$availability->breakTimeRequired;   // int (minutes)
$availability->calculatedAt;        // CarbonImmutable
</code-snippet>
@endverbatim

#### HosViolation

@verbatim
<code-snippet name="HosViolation DTO" lang="php">
use Motive\Data\HosViolation;
use Motive\Enums\HosViolationType;

$violation->id;         // int
$violation->driverId;   // int
$violation->type;       // HosViolationType enum
$violation->startTime;  // CarbonImmutable
$violation->endTime;    // ?CarbonImmutable
$violation->duration;   // ?int (minutes)
</code-snippet>
@endverbatim

#### InspectionReport

@verbatim
<code-snippet name="InspectionReport DTO" lang="php">
use Motive\Data\InspectionReport;
use Motive\Enums\InspectionType;
use Motive\Enums\InspectionStatus;

$report->id;          // int
$report->driverId;    // int
$report->vehicleId;   // int
$report->type;        // InspectionType enum
$report->status;      // InspectionStatus enum
$report->defects;     // array<InspectionDefect>
$report->signature;   // ?string
$report->createdAt;   // CarbonImmutable
</code-snippet>
@endverbatim

### Dispatch Entities

#### Dispatch

@verbatim
<code-snippet name="Dispatch DTO" lang="php">
use Motive\Data\Dispatch;
use Motive\Enums\DispatchStatus;

$dispatch->id;          // int
$dispatch->externalId;  // ?string
$dispatch->driverId;    // ?int
$dispatch->vehicleId;   // ?int
$dispatch->status;      // DispatchStatus enum
$dispatch->stops;       // array<DispatchStop>
$dispatch->createdAt;   // CarbonImmutable
</code-snippet>
@endverbatim

#### DispatchStop

@verbatim
<code-snippet name="DispatchStop DTO" lang="php">
use Motive\Data\DispatchStop;
use Motive\Enums\StopType;

$stop->id;              // int
$stop->type;            // StopType enum
$stop->address;         // string
$stop->latitude;        // ?float
$stop->longitude;       // ?float
$stop->scheduledAt;     // ?CarbonImmutable
$stop->arrivedAt;       // ?CarbonImmutable
$stop->departedAt;      // ?CarbonImmutable
</code-snippet>
@endverbatim

### Document Entities

#### Document

@verbatim
<code-snippet name="Document DTO" lang="php">
use Motive\Data\Document;
use Motive\Enums\DocumentType;
use Motive\Enums\DocumentStatus;

$doc->id;         // int
$doc->driverId;   // ?int
$doc->type;       // DocumentType enum
$doc->status;     // DocumentStatus enum
$doc->name;       // string
$doc->images;     // array<DocumentImage>
$doc->createdAt;  // CarbonImmutable
</code-snippet>
@endverbatim

### Webhook Entities

#### Webhook

@verbatim
<code-snippet name="Webhook DTO" lang="php">
use Motive\Data\Webhook;
use Motive\Enums\WebhookStatus;

$webhook->id;        // int
$webhook->url;       // string
$webhook->events;    // array
$webhook->status;    // WebhookStatus enum
$webhook->secret;    // ?string
$webhook->createdAt; // CarbonImmutable
</code-snippet>
@endverbatim

### Enums

#### DutyStatus

@verbatim
<code-snippet name="DutyStatus enum" lang="php">
use Motive\Enums\DutyStatus;

DutyStatus::OffDuty;           // 'off_duty'
DutyStatus::SleeperBerth;      // 'sleeper_berth'
DutyStatus::Driving;           // 'driving'
DutyStatus::OnDuty;            // 'on_duty'
DutyStatus::PersonalConveyance; // 'personal_conveyance'
DutyStatus::YardMove;          // 'yard_move'
</code-snippet>
@endverbatim

#### VehicleStatus

@verbatim
<code-snippet name="VehicleStatus enum" lang="php">
use Motive\Enums\VehicleStatus;

VehicleStatus::Active;         // 'active'
VehicleStatus::Inactive;       // 'inactive'
VehicleStatus::Decommissioned; // 'decommissioned'
</code-snippet>
@endverbatim

#### DispatchStatus

@verbatim
<code-snippet name="DispatchStatus enum" lang="php">
use Motive\Enums\DispatchStatus;

DispatchStatus::Pending;    // 'pending'
DispatchStatus::InProgress; // 'in_progress'
DispatchStatus::Completed;  // 'completed'
DispatchStatus::Cancelled;  // 'cancelled'
</code-snippet>
@endverbatim

#### WebhookEvent

@verbatim
<code-snippet name="WebhookEvent enum" lang="php">
use Motive\Enums\WebhookEvent;

WebhookEvent::VehicleLocationUpdated;
WebhookEvent::VehicleCreated;
WebhookEvent::VehicleUpdated;
WebhookEvent::HosLogCreated;
WebhookEvent::HosViolationDetected;
WebhookEvent::DriverCreated;
WebhookEvent::InspectionCreated;
// ... and more
</code-snippet>
@endverbatim

### Working with DTOs

#### Accessing Properties

@verbatim
<code-snippet name="DTO property access" lang="php">
// Direct property access
$name = $vehicle->number;

// Array access
$name = $vehicle['number'];

// toArray conversion
$array = $vehicle->toArray();

// JSON serialization
$json = json_encode($vehicle);
</code-snippet>
@endverbatim

#### Creating DTOs from Arrays

@verbatim
<code-snippet name="Create DTO from array" lang="php">
use Motive\Data\Vehicle;

// Static factory method
$vehicle = Vehicle::from([
    'id' => 123,
    'number' => 'TRUCK-001',
    'status' => 'active',
    'created_at' => '2024-01-15T10:00:00Z',
]);

// Automatic snake_case to camelCase conversion
// 'created_at' becomes $vehicle->createdAt
</code-snippet>
@endverbatim

#### Type Casting

DTOs automatically cast properties:
- `int`, `float`, `string`, `bool` - primitive casting
- `CarbonImmutable` - datetime parsing
- Enum classes - backed enum casting
- Nested DTOs - recursive hydration
- Arrays of DTOs - collection casting

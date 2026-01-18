# Architecture

The Motive SDK is built with a clean, layered architecture that follows Laravel conventions and best practices.

## Overview

```
┌─────────────────────────────────────────────────────────┐
│                    Your Application                      │
├─────────────────────────────────────────────────────────┤
│                  Motive Facade / Manager                 │
├─────────────────────────────────────────────────────────┤
│                      Resources                           │
│   (VehiclesResource, UsersResource, HosLogsResource)    │
├─────────────────────────────────────────────────────────┤
│                    MotiveClient                          │
│              (HTTP client, authentication)               │
├─────────────────────────────────────────────────────────┤
│                Data Transfer Objects                     │
│            (Vehicle, User, HosLog, etc.)                │
├─────────────────────────────────────────────────────────┤
│                       Enums                              │
│     (VehicleStatus, DutyStatus, WebhookEvent)           │
└─────────────────────────────────────────────────────────┘
```

## Key Components

### MotiveManager

The `MotiveManager` class is the entry point for all SDK interactions. It:

- Manages API connections and authentication
- Provides access to all 31 resource classes
- Supports context modifiers (timezone, metric units, user ID)
- Handles multi-tenant configurations

```php
use Motive\MotiveManager;

// Via dependency injection
public function __construct(protected MotiveManager $motive) {}

// Or via facade
use Motive\Facades\Motive;
Motive::vehicles()->list();
```

### Resources

Resources are specialized classes that handle API operations for a specific endpoint. Each resource:

- Extends the base `Resource` class
- Provides methods like `list()`, `find()`, `create()`, `update()`, `delete()`
- Converts API responses to strongly-typed DTOs
- Handles pagination automatically

There are **31 resources** available:

| Category | Resources |
|----------|-----------|
| Fleet | vehicles, assets, vehicleGateways, faultCodes |
| Drivers | users, drivingPeriods, scorecard |
| Compliance | hosLogs, hosAvailability, hosViolations, inspectionReports, iftaReports |
| Dispatch | dispatches, locations, geofences |
| Communication | messages, documents |
| Safety | driverPerformanceEvents, cameraConnections, cameraControl |
| Operations | groups, companies, forms, formEntries, timecards, utilization |
| Financial | fuelPurchases, motiveCard |
| Integration | webhooks, externalIds, freightVisibility, reeferActivity |

### Data Transfer Objects (DTOs)

DTOs are immutable objects that represent API response data. They:

- Extend the base `DataTransferObject` class
- Provide type-safe property access
- Automatically cast values to appropriate types (enums, dates, nested DTOs)
- Support serialization to arrays/JSON

There are **50 DTOs** covering all API response types.

```php
$vehicle = Motive::vehicles()->find(123);

// Type-safe access
$vehicle->id;        // int
$vehicle->number;    // string
$vehicle->status;    // VehicleStatus enum
$vehicle->createdAt; // CarbonImmutable
```

### Enums

Backed enums provide type safety for status and type values. The SDK includes **26 enums**:

| Category | Enums |
|----------|-------|
| Status | VehicleStatus, AssetStatus, UserStatus, DispatchStatus, DocumentStatus, InspectionStatus, WebhookStatus, TimecardStatus, VideoStatus, ShipmentStatus |
| Types | AssetType, GeofenceType, StopType, DocumentType, InspectionType, PerformanceEventType, FormFieldType, CardTransactionType, CameraType |
| HOS | DutyStatus, HosViolationType |
| Other | UserRole, EventSeverity, MessageDirection, Scope, WebhookEvent |

```php
use Motive\Enums\VehicleStatus;
use Motive\Enums\DutyStatus;

// Enum comparisons
if ($vehicle->status === VehicleStatus::Active) {
    // ...
}

// Use enum values in requests
$logs = Motive::hosLogs()->list([
    'duty_status' => DutyStatus::Driving->value,
]);
```

### MotiveClient

The HTTP client handles all low-level communication:

- Sends authenticated HTTP requests
- Handles request retries with exponential backoff
- Converts error responses to appropriate exceptions
- Supports custom timeout and retry configurations

## Request Flow

1. **Your code** calls a resource method: `Motive::vehicles()->list()`
2. **MotiveManager** returns the appropriate Resource instance
3. **Resource** constructs the API request with proper path and parameters
4. **MotiveClient** sends the authenticated HTTP request
5. **API Response** is received and validated
6. **Resource** converts the response to DTOs
7. **DTOs** are returned to your code with typed properties

## Error Handling

The SDK uses a hierarchy of exception classes:

```
MotiveException
├── AuthenticationException (401)
├── AuthorizationException (403)
├── NotFoundException (404)
├── ValidationException (422)
├── RateLimitException (429)
├── ServerException (5xx)
└── WebhookVerificationException
```

All exceptions include access to:
- HTTP status code
- Error message
- Full response body
- Original response object

## Extensibility

The SDK is designed for extension:

- **Macros**: Add custom methods to resource classes
- **Custom authenticators**: Implement the `Authenticator` contract
- **Custom token stores**: Implement the `TokenStore` contract
- **Raw requests**: Access the underlying HTTP client directly

## Design Principles

1. **Laravel-first**: Follows Laravel conventions and integrates seamlessly
2. **Type safety**: Strong typing with PHP 8.2+ features
3. **Immutability**: DTOs are immutable after creation
4. **Testability**: Built-in faking and factory support
5. **Memory efficiency**: Lazy pagination for large datasets
6. **Fail-fast**: Clear exceptions for all error conditions

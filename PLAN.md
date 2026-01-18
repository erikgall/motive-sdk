# Motive ELD Laravel SDK - Implementation Plan

## Overview

A first-party-quality Laravel SDK for the Motive ELD API following Taylor Otwell's design philosophy: expressive, elegant syntax with fluent, chainable methods.

### Key Decisions

- **Package**: `erikgall/motive-sdk` with `Motive\` namespace
- **PHP**: 8.2+ (enums, property promotion, typed properties)
- **Laravel**: 11+
- **Auth**: Both API Key and OAuth 2.0
- **Versioning**: SDK abstracts API versions internally (uses latest per resource)
- **Multi-tenancy**: Supports multiple named connections
- **Token Storage**: User implements `TokenStore` contract

---

## Directory Structure

```
motive-sdk/
├── composer.json
├── config/
│   └── motive.php
├── src/
│   ├── MotiveServiceProvider.php
│   ├── MotiveManager.php
│   ├── Facades/
│   │   └── Motive.php
│   ├── Contracts/
│   │   ├── Authenticator.php
│   │   └── TokenStore.php
│   ├── Client/
│   │   ├── MotiveClient.php
│   │   ├── PendingRequest.php
│   │   └── Response.php
│   ├── Auth/
│   │   ├── ApiKeyAuthenticator.php
│   │   ├── OAuthAuthenticator.php
│   │   └── AccessToken.php
│   ├── Resources/
│   │   ├── Concerns/
│   │   │   ├── HasCrudOperations.php
│   │   │   ├── HasPagination.php
│   │   │   └── HasExternalIdLookup.php
│   │   ├── Resource.php
│   │   └── [31 Resource Classes - see below]
│   ├── Data/
│   │   ├── Concerns/
│   │   │   └── HasFactory.php
│   │   ├── DataTransferObject.php
│   │   └── [25+ DTO Classes]
│   ├── Enums/
│   │   ├── DutyStatus.php
│   │   ├── VehicleStatus.php
│   │   ├── DispatchStatus.php
│   │   ├── DocumentStatus.php
│   │   ├── WebhookEvent.php
│   │   └── Scope.php
│   ├── Pagination/
│   │   ├── Paginator.php
│   │   ├── PaginatedResponse.php
│   │   └── LazyPaginator.php
│   ├── Webhooks/
│   │   ├── WebhookSignature.php
│   │   └── WebhookPayload.php
│   ├── Http/
│   │   └── Middleware/
│   │       └── VerifyWebhookSignature.php
│   ├── Exceptions/
│   │   ├── MotiveException.php
│   │   ├── AuthenticationException.php
│   │   ├── AuthorizationException.php
│   │   ├── ValidationException.php
│   │   ├── RateLimitException.php
│   │   ├── NotFoundException.php
│   │   ├── ServerException.php
│   │   └── WebhookVerificationException.php
│   └── Testing/
│       ├── MotiveFake.php
│       ├── FakeResponse.php
│       └── Factories/
│           └── [DTO Factories]
└── tests/
    ├── TestCase.php
    ├── Unit/
    └── Feature/
```

---

## API Resources (31 Total)

| Resource | Endpoints | API Version |
|----------|-----------|-------------|
| Assets | 7 | v1 |
| Vehicles | 6 | v1 |
| Users | 7 | v1 |
| Dispatches | 3 | v3 |
| DispatchLocations | 4 | v1 |
| HosLogs | 7 | v1 |
| HosAvailability | 1 | v1 |
| HosViolations | 1 | v1 |
| DriverPerformanceEvents | 3 | v2 |
| Geofences | 8 | v1 |
| Groups | 13 | v1 |
| Locations | 9 | v3 |
| Messages | 5 | v1 |
| Documents | 10 | v1 |
| InspectionReports | 6 | v2 |
| FuelPurchases | 6 | v1 |
| MotiveCard | 9 | v1 |
| Webhooks | 8 | v2 |
| FreightVisibility | 12 | v1 |
| IftaReports | 2 | v1 |
| FaultCodes | 1 | v1 |
| Forms | 1 | v1 |
| FormEntries | 2 | v1 |
| Timecards | 4 | v1 |
| Utilization | 4 | v1 |
| Scorecard | 1 | v1 |
| Companies | 1 | v1 |
| CameraConnections | 1 | v1 |
| CameraControl | 2 | v1 |
| ExternalIds | 3 | v1 |
| DrivingPeriods | 3 | v1 |
| VehicleGateways | 1 | v1 |
| ReeferActivity | 2 | v1 |

---

## Core Architecture

### 1. MotiveManager (Entry Point)

```php
// Fluent API with immutable context
Motive::withTimezone('America/Chicago')
    ->withMetricUnits()
    ->withUserId(123)
    ->vehicles()
    ->list();

// Multiple connections
Motive::connection('tenant-a')->vehicles()->list();

// Dynamic authentication
Motive::withApiKey('key')->vehicles()->list();
Motive::withOAuth($token, $refresh, $expires)->users()->list();
```

### 2. Resource Pattern

```php
// Base Resource with shared concerns
abstract class Resource {
    use HasCrudOperations, HasPagination, HasExternalIdLookup;

    abstract protected function basePath(): string;
    abstract protected function resourceKey(): string;
    abstract protected function dtoClass(): string;
}

// Concrete implementation
class VehiclesResource extends Resource {
    protected string $apiVersion = '1';

    public function list(): LazyCollection { /* auto-paginating */ }
    public function paginate(int $page = 1): PaginatedResponse { /* with metadata */ }
    public function find(int|string $id): Vehicle { /* single resource */ }
    public function create(array $data): Vehicle { /* POST */ }
    public function update(int|string $id, array $data): Vehicle { /* PUT */ }
    public function delete(int|string $id): bool { /* DELETE */ }
}
```

### 3. DTOs (Data Transfer Objects)

```php
class Vehicle extends DataTransferObject {
    public function __construct(
        public int $id,
        public string $companyId,
        public ?string $number,
        public VehicleStatus $status,
        public ?Driver $currentDriver,
        public ?CarbonImmutable $createdAt,
        // ... all fields typed
    ) {}

    public static function from(array $data): static { /* hydration */ }
}
```

### 4. Lazy Pagination

```php
// Memory-efficient iteration over all pages
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    // Automatically fetches next page when needed
}

// Or collect with metadata
$page = Motive::vehicles()->paginate(page: 2, perPage: 50);
echo "Page {$page->currentPage()} of {$page->lastPage()}";
```

### 5. Webhook Handling

```php
// Middleware for signature verification
Route::post('/webhooks/motive', WebhookController::class)
    ->middleware('motive.webhook');

// Typed webhook payload
$payload = WebhookPayload::fromRequest($request);
match ($payload->event) {
    WebhookEvent::VehicleLocationUpdated => $this->handleLocation($payload),
    WebhookEvent::HosViolationDetected => $this->handleViolation($payload),
};
```

### 6. Testing Support

```php
// Fake for application testing
Motive::fake([
    'vehicles' => Vehicle::factory()->count(5)->make(),
]);

$this->artisan('sync:vehicles');

Motive::assertRequestedVehicles();
Motive::assertRequestCount(1);
```

---

## Configuration Schema

```php
// config/motive.php
return [
    'default' => env('MOTIVE_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'auth_driver' => env('MOTIVE_AUTH_DRIVER', 'api_key'),
            'api_key' => env('MOTIVE_API_KEY'),
            'oauth' => [
                'client_id' => env('MOTIVE_CLIENT_ID'),
                'client_secret' => env('MOTIVE_CLIENT_SECRET'),
                'redirect_uri' => env('MOTIVE_REDIRECT_URI'),
            ],
            'base_url' => env('MOTIVE_BASE_URL', 'https://api.gomotive.com'),
            'timeout' => 30,
            'retry' => ['times' => 3, 'sleep' => 100],
        ],
    ],

    'headers' => [
        'timezone' => env('MOTIVE_TIMEZONE'),
        'metric_units' => env('MOTIVE_METRIC_UNITS', false),
    ],

    'webhooks' => [
        'secret' => env('MOTIVE_WEBHOOK_SECRET'),
        'tolerance' => 300,
    ],
];
```

---

## Implementation Phases

### Phase 1: Foundation (Core Infrastructure)

**Files to create:**
- `src/MotiveServiceProvider.php`
- `src/MotiveManager.php`
- `src/Facades/Motive.php`
- `src/Client/MotiveClient.php`
- `src/Client/PendingRequest.php`
- `src/Client/Response.php`
- `src/Contracts/Authenticator.php`
- `src/Contracts/TokenStore.php`
- `src/Auth/ApiKeyAuthenticator.php`
- `src/Exceptions/*.php` (all 8 exception classes)
- `src/Resources/Resource.php`
- `src/Resources/Concerns/*.php` (3 traits)
- `src/Data/DataTransferObject.php`
- `src/Data/Concerns/HasFactory.php`
- `src/Pagination/Paginator.php`
- `src/Pagination/PaginatedResponse.php`
- `src/Pagination/LazyPaginator.php`
- `config/motive.php`
- `composer.json` (update dependencies)

### Phase 2: Essential Resources

**Files to create:**
- `src/Resources/Vehicles/VehiclesResource.php`
- `src/Resources/Users/UsersResource.php`
- `src/Resources/Assets/AssetsResource.php`
- `src/Resources/Companies/CompaniesResource.php`
- `src/Data/Vehicle.php`
- `src/Data/User.php`
- `src/Data/Driver.php`
- `src/Data/Asset.php`
- `src/Data/Company.php`
- `src/Data/VehicleLocation.php`
- `src/Enums/VehicleStatus.php`
- `src/Enums/AssetStatus.php`
- `src/Enums/UserRole.php`
- `src/Enums/UserStatus.php`

### Phase 3: HOS & Compliance

**Files to create:**
- `src/Resources/HoursOfService/HosLogsResource.php`
- `src/Resources/HoursOfService/HosAvailabilityResource.php`
- `src/Resources/HoursOfService/HosViolationsResource.php`
- `src/Resources/InspectionReports/InspectionReportsResource.php`
- `src/Resources/FaultCodes/FaultCodesResource.php`
- `src/Data/HosLog.php`
- `src/Data/HosAvailability.php`
- `src/Data/HosViolation.php`
- `src/Data/InspectionReport.php`
- `src/Data/InspectionDefect.php`
- `src/Data/FaultCode.php`
- `src/Enums/DutyStatus.php`
- `src/Enums/HosViolationType.php`
- `src/Enums/InspectionType.php`
- `src/Enums/InspectionStatus.php`

### Phase 4: Dispatch & Location

**Files to create:**
- `src/Resources/Dispatches/DispatchesResource.php`
- `src/Resources/DispatchLocations/DispatchLocationsResource.php`
- `src/Resources/Locations/LocationsResource.php`
- `src/Resources/Geofences/GeofencesResource.php`
- `src/Resources/Groups/GroupsResource.php`
- `src/Data/Dispatch.php`
- `src/Data/DispatchStop.php`
- `src/Data/DispatchLocation.php`
- `src/Data/Location.php`
- `src/Data/Geofence.php`
- `src/Data/GeofenceCoordinate.php`
- `src/Data/Group.php`
- `src/Data/GroupMember.php`
- `src/Enums/DispatchStatus.php`
- `src/Enums/StopType.php`
- `src/Enums/GeofenceType.php`

### Phase 5: OAuth & Webhooks

**Files to create:**
- `src/Auth/OAuthAuthenticator.php`
- `src/Auth/AccessToken.php`
- `src/Auth/OAuthFlow.php`
- `src/Webhooks/WebhookSignature.php`
- `src/Webhooks/WebhookPayload.php`
- `src/Http/Middleware/VerifyWebhookSignature.php`
- `src/Resources/Webhooks/WebhooksResource.php`
- `src/Data/Webhook.php`
- `src/Data/WebhookLog.php`
- `src/Enums/WebhookEvent.php`
- `src/Enums/WebhookStatus.php`
- `src/Enums/Scope.php`
- `src/Exceptions/WebhookVerificationException.php`

### Phase 6: Communication & Documents

**Files to create:**
- `src/Resources/Messages/MessagesResource.php`
- `src/Resources/Documents/DocumentsResource.php`
- `src/Data/Message.php`
- `src/Data/Document.php`
- `src/Data/DocumentImage.php`
- `src/Enums/MessageDirection.php`
- `src/Enums/DocumentType.php`
- `src/Enums/DocumentStatus.php`

### Phase 7: Fuel & Reporting

**Files to create:**
- `src/Resources/FuelPurchases/FuelPurchasesResource.php`
- `src/Resources/IftaReports/IftaReportsResource.php`
- `src/Resources/DriverPerformance/DriverPerformanceEventsResource.php`
- `src/Resources/Scorecard/ScorecardResource.php`
- `src/Resources/Utilization/UtilizationResource.php`
- `src/Data/FuelPurchase.php`
- `src/Data/IftaReport.php`
- `src/Data/IftaJurisdiction.php`
- `src/Data/DriverPerformanceEvent.php`
- `src/Data/Scorecard.php`
- `src/Data/UtilizationReport.php`
- `src/Data/UtilizationDay.php`
- `src/Enums/PerformanceEventType.php`
- `src/Enums/EventSeverity.php`

### Phase 8: Time & Forms

**Files to create:**
- `src/Resources/Timecards/TimecardsResource.php`
- `src/Resources/Forms/FormsResource.php`
- `src/Resources/Forms/FormEntriesResource.php`
- `src/Resources/DrivingPeriods/DrivingPeriodsResource.php`
- `src/Data/Timecard.php`
- `src/Data/TimecardEntry.php`
- `src/Data/Form.php`
- `src/Data/FormEntry.php`
- `src/Data/FormField.php`
- `src/Data/DrivingPeriod.php`
- `src/Enums/TimecardStatus.php`
- `src/Enums/FormFieldType.php`

### Phase 9: Advanced Resources

**Files to create:**
- `src/Resources/MotiveCard/MotiveCardResource.php`
- `src/Resources/FreightVisibility/FreightVisibilityResource.php`
- `src/Resources/Camera/CameraConnectionsResource.php`
- `src/Resources/Camera/CameraControlResource.php`
- `src/Resources/ExternalIds/ExternalIdsResource.php`
- `src/Resources/VehicleGateways/VehicleGatewaysResource.php`
- `src/Resources/ReeferActivity/ReeferActivityResource.php`
- `src/Data/MotiveCard.php`
- `src/Data/CardTransaction.php`
- `src/Data/CardLimit.php`
- `src/Data/Shipment.php`
- `src/Data/ShipmentTracking.php`
- `src/Data/ShipmentEta.php`
- `src/Data/CameraConnection.php`
- `src/Data/VideoRequest.php`
- `src/Data/Video.php`
- `src/Data/ExternalId.php`
- `src/Data/VehicleGateway.php`
- `src/Data/ReeferActivity.php`
- `src/Enums/CardTransactionType.php`
- `src/Enums/ShipmentStatus.php`
- `src/Enums/CameraType.php`
- `src/Enums/VideoStatus.php`

### Phase 10: Testing Infrastructure

**Files to create:**
- `src/Testing/MotiveFake.php`
- `src/Testing/FakeResponse.php`
- `src/Testing/RequestHistory.php`
- `src/Testing/Factories/Factory.php`
- `src/Testing/Factories/VehicleFactory.php`
- `src/Testing/Factories/UserFactory.php`
- `src/Testing/Factories/DriverFactory.php`
- `src/Testing/Factories/AssetFactory.php`
- `src/Testing/Factories/HosLogFactory.php`
- `src/Testing/Factories/DispatchFactory.php`
- `src/Testing/Factories/LocationFactory.php`
- `src/Testing/Factories/DocumentFactory.php`
- `src/Testing/Factories/MessageFactory.php`
- `src/Testing/Factories/WebhookFactory.php`
- `tests/TestCase.php`
- `tests/Unit/ClientTest.php`
- `tests/Unit/PaginationTest.php`
- `tests/Unit/AuthenticationTest.php`
- `tests/Unit/WebhookSignatureTest.php`
- `tests/Unit/DataTransferObjectTest.php`
- `tests/Feature/VehiclesResourceTest.php`
- `tests/Feature/UsersResourceTest.php`
- `tests/Feature/HosResourceTest.php`
- `tests/Feature/DispatchesResourceTest.php`
- `tests/Feature/WebhooksResourceTest.php`

### Phase 11: Documentation & Polish

**Files to create/update:**
- `README.md` (comprehensive examples)
- `CHANGELOG.md`
- `CONTRIBUTING.md`
- `LICENSE.md`
- PHPDoc blocks on all public methods
- Static analysis configuration (PHPStan level 8)
- Code style configuration (Laravel Pint)

### AI-Friendly Documentation (Laravel Boost)

Following [Laravel's AI Package Guidelines](https://laravel.com/docs/12.x/ai#package-guidelines) for Boost compatibility:

**Directory Structure:**
```
resources/
└── boost/
    └── guidelines/
        ├── core.blade.php          # Main SDK overview & quick start
        ├── resources.blade.php     # All resources with methods
        ├── query-builder.blade.php # Query builder reference
        ├── entities.blade.php      # DTOs and data structures
        └── testing.blade.php       # Testing with SamsaraFake
```

**Format (Blade templates with code snippets):**
```blade
## Samsara SDK

This package provides a fluent Laravel SDK for the Samsara Fleet Management API.

### Features

- Fluent Query Builder: Filter, paginate, and stream API results. Example:

@verbatim
<code-snippet name="Query drivers by tag" lang="php">
$drivers = Samsara::drivers()
    ->query()
    ->whereTag(['fleet-a', 'fleet-b'])
    ->limit(50)
    ->get();
</code-snippet>
@endverbatim

- Resource Access: 40+ resources covering 197 API endpoints. Example:

@verbatim
<code-snippet name="Get vehicle stats" lang="php">
$stats = Samsara::vehicleStats()
    ->current()
    ->withGps()
    ->withEngineStates()
    ->get();
</code-snippet>
@endverbatim
```

**Benefits:**
- Automatic discovery when users run `boost:install`
- AI agents understand SDK patterns and best practices
- Proper code generation for Samsara API integration

---

## Verification Plan

### Unit Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suites
./vendor/bin/phpunit --testsuite=Unit
./vendor/bin/phpunit --testsuite=Feature
```

### Static Analysis

```bash
./vendor/bin/phpstan analyse --level=8
```

### Code Style

```bash
./vendor/bin/pint
```

### Manual Testing

```php
// 1. Test API Key auth
$vehicles = Motive::vehicles()->list()->take(5);
foreach ($vehicles as $v) {
    dump($v->number, $v->status);
}

// 2. Test pagination
$page = Motive::vehicles()->paginate(page: 1, perPage: 10);
dump($page->total(), $page->items());

// 3. Test CRUD
$vehicle = Motive::vehicles()->find(123);
$updated = Motive::vehicles()->update(123, ['number' => 'TRUCK-001']);

// 4. Test multiple connections
$a = Motive::connection('tenant-a')->vehicles()->list();
$b = Motive::connection('tenant-b')->vehicles()->list();

// 5. Test webhooks (via ngrok or similar)
// POST to /webhooks/motive with signature header
```

---

## Design Principles

### 1. Fluent, Chainable API

All context modifiers return new instances to allow chaining:

```php
Motive::connection('tenant')
    ->withTimezone('America/Chicago')
    ->withMetricUnits()
    ->vehicles()
    ->list();
```

### 2. Immutability

DTOs are treated as immutable data structures. Context changes create new manager instances.

### 3. Type Safety

- All DTOs have typed properties
- Enums for all status/type fields
- Carbon for all datetime fields
- Strict return types on all methods

### 4. Memory Efficiency

Lazy pagination via generators for large datasets:

```php
// Iterates without loading all records into memory
foreach (Motive::vehicles()->list() as $vehicle) {
    // Process one at a time
}
```

### 5. Error Handling

Specific exception classes for different error types:

```php
try {
    $vehicle = Motive::vehicles()->find(123);
} catch (NotFoundException $e) {
    // 404
} catch (RateLimitException $e) {
    // 429 with retry info
} catch (ValidationException $e) {
    // 422 with field errors
}
```

### 6. Testability

First-class testing support with fakes and factories:

```php
Motive::fake([
    'vehicles' => Vehicle::factory()->count(3)->make(),
]);

// Test code...

Motive::assertRequested('vehicles.list');
```

---

## Usage Examples

### Basic Operations

```php
use Motive\Facades\Motive;

// List with lazy pagination
foreach (Motive::vehicles()->list() as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}

// CRUD
$vehicle = Motive::vehicles()->find(123);
$vehicle = Motive::vehicles()->findByNumber('TRUCK-001');
$vehicle = Motive::vehicles()->create([...]);
$vehicle = Motive::vehicles()->update(123, [...]);
Motive::vehicles()->delete(123);
```

### HOS Compliance

```php
// Get driver availability
$availability = Motive::hosAvailability()->forDriver(123);
echo "Drive time: {$availability->driveTimeRemaining} min";

// Check violations
$violations = Motive::hosViolations()->list([
    'driver_ids' => [123],
    'start_date' => now()->subDays(30),
]);
```

### Dispatch Management

```php
$dispatch = Motive::dispatches()->create([
    'external_id' => 'ORDER-123',
    'driver_id' => 456,
]);

Motive::dispatchLocations()->create($dispatch->id, [
    'type' => 'pickup',
    'address' => '123 Main St',
]);
```

### OAuth Flow

```php
// Generate authorization URL
$url = Motive::oauth()->authorizationUrl(
    scopes: [Scope::VehiclesRead, Scope::UsersRead],
);

// Exchange code for tokens
$tokens = Motive::oauth()->exchangeCode($code);

// Use with token
Motive::withOAuth($tokens->accessToken, $tokens->refreshToken)
    ->vehicles()->list();
```

# Motive ELD Laravel SDK

A first-party-quality Laravel SDK for the [Motive ELD API](https://developer.gomotive.com/) featuring expressive, elegant syntax with fluent, chainable methods.

## Requirements

- PHP 8.2+
- Laravel 11+

## Installation

```bash
composer require erikgall/motive-sdk
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=motive-config
```

## Configuration

Add your Motive API credentials to your `.env` file:

```env
# API Key Authentication
MOTIVE_API_KEY=your-api-key

# OAuth Authentication (optional)
MOTIVE_CLIENT_ID=your-client-id
MOTIVE_CLIENT_SECRET=your-client-secret
MOTIVE_REDIRECT_URI=https://your-app.com/motive/callback

# Webhook Secret (optional)
MOTIVE_WEBHOOK_SECRET=your-webhook-secret

# Optional Settings
MOTIVE_TIMEZONE=America/Chicago
MOTIVE_METRIC_UNITS=false
```

### Configuration File

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

## Basic Usage

### Using the Facade

```php
use Motive\Facades\Motive;

// List all vehicles
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}
```

### Using Dependency Injection

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

---

## Vehicles

### List All Vehicles

```php
use Motive\Facades\Motive;

// Lazy pagination - automatically fetches pages as needed
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo $vehicle->number;
    echo $vehicle->make;
    echo $vehicle->model;
    echo $vehicle->vin;
    echo $vehicle->status->value; // VehicleStatus enum
}

// With filters
$vehicles = Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);
```

### Paginate Vehicles

```php
// Get a specific page with metadata
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);

echo "Showing {$page->count()} of {$page->total()} vehicles";
echo "Page {$page->currentPage()} of {$page->lastPage()}";

foreach ($page->items() as $vehicle) {
    echo $vehicle->number;
}

// Check for more pages
if ($page->hasMorePages()) {
    $nextPage = Motive::vehicles()->paginate(page: $page->currentPage() + 1);
}
```

### Find a Vehicle

```php
// By ID
$vehicle = Motive::vehicles()->find(123);

// By vehicle number
$vehicle = Motive::vehicles()->findByNumber('TRUCK-001');

// By external ID
$vehicle = Motive::vehicles()->findByExternalId('ext-123');
```

### Create a Vehicle

```php
$vehicle = Motive::vehicles()->create([
    'number' => 'TRUCK-042',
    'make' => 'Freightliner',
    'model' => 'Cascadia',
    'year' => 2024,
    'vin' => '1FUJGLDR5CLBP8834',
    'license_plate_number' => 'ABC1234',
    'license_plate_state' => 'TX',
]);

echo "Created vehicle #{$vehicle->id}";
```

### Update a Vehicle

```php
$vehicle = Motive::vehicles()->update(123, [
    'number' => 'TRUCK-042-UPDATED',
    'license_plate_number' => 'XYZ9876',
]);
```

### Delete a Vehicle

```php
$deleted = Motive::vehicles()->delete(123);

if ($deleted) {
    echo "Vehicle deleted successfully";
}
```

### Get Current Location

```php
$location = Motive::vehicles()->currentLocation(123);

echo "Lat: {$location->latitude}, Lng: {$location->longitude}";
echo "Speed: {$location->speed} mph";
echo "Heading: {$location->bearing}";
echo "Updated: {$location->locatedAt->diffForHumans()}";
```

### Get Location History

```php
$locations = Motive::vehicles()->locations(123, [
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($locations as $location) {
    echo "{$location->locatedAt}: ({$location->latitude}, {$location->longitude})";
}
```

---

## Users & Drivers

### List Users

```php
$users = Motive::users()->list();

foreach ($users as $user) {
    echo $user->firstName . ' ' . $user->lastName;
    echo $user->email;
    echo $user->role;
}

// Filter by role
$drivers = Motive::users()->list(['role' => 'driver']);
```

### Find a User

```php
$user = Motive::users()->find(456);

// Access driver-specific data
if ($user->driver) {
    echo "Driver License: {$user->driver->licenseNumber}";
    echo "License State: {$user->driver->licenseState}";
}
```

### Create a User

```php
$user = Motive::users()->create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '555-123-4567',
    'role' => 'driver',
    'driver' => [
        'license_number' => 'DL123456',
        'license_state' => 'TX',
    ],
]);
```

### Update a User

```php
$user = Motive::users()->update(456, [
    'phone' => '555-987-6543',
]);
```

### Deactivate/Reactivate a User

```php
// Deactivate
Motive::users()->deactivate(456);

// Reactivate
Motive::users()->reactivate(456);
```

---

## Hours of Service (HOS)

### Get HOS Logs

```php
use Motive\Enums\DutyStatus;

$logs = Motive::hosLogs()->list([
    'driver_ids' => [123, 456],
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($logs as $log) {
    echo $log->driver->firstName . ' ' . $log->driver->lastName;
    echo $log->status->value; // DutyStatus enum
    echo $log->startTime->format('Y-m-d H:i:s');
    echo $log->duration . ' minutes';
    echo $log->location;
}

// Filter by duty status
$drivingLogs = Motive::hosLogs()->list([
    'driver_ids' => [123],
    'duty_status' => DutyStatus::Driving->value,
]);
```

### Get HOS Availability

```php
$availability = Motive::hosAvailability()->forDriver(123);

echo "Drive time remaining: {$availability->driveTimeRemaining} minutes";
echo "Shift time remaining: {$availability->shiftTimeRemaining} minutes";
echo "Cycle time remaining: {$availability->cycleTimeRemaining} minutes";
echo "Break time required in: {$availability->breakTimeRequired} minutes";

// Check if driver can drive
if ($availability->driveTimeRemaining > 0 && $availability->shiftTimeRemaining > 0) {
    echo "Driver is available to drive";
}
```

### Get HOS Availability for Multiple Drivers

```php
$availabilities = Motive::hosAvailability()->list([
    'driver_ids' => [123, 456, 789],
]);

foreach ($availabilities as $availability) {
    echo "{$availability->driver->firstName}: {$availability->driveTimeRemaining} min remaining";
}
```

### Get HOS Violations

```php
$violations = Motive::hosViolations()->list([
    'driver_ids' => [123],
    'start_date' => now()->subDays(30)->toDateString(),
]);

foreach ($violations as $violation) {
    echo $violation->type; // e.g., "11_hour", "14_hour", "30_minute_break"
    echo $violation->startTime->format('Y-m-d H:i');
    echo $violation->duration . ' minutes';
}
```

### Create/Edit HOS Log Entry

```php
// Create a new log entry
$log = Motive::hosLogs()->create([
    'driver_id' => 123,
    'status' => DutyStatus::OnDuty->value,
    'start_time' => now()->toIso8601String(),
    'location' => 'Dallas, TX',
    'notes' => 'Pre-trip inspection',
]);

// Edit an existing entry (with annotation)
$log = Motive::hosLogs()->update($log->id, [
    'status' => DutyStatus::Driving->value,
    'annotation' => 'Corrected status from On Duty to Driving',
]);
```

---

## Dispatches

### List Dispatches

```php
use Motive\Enums\DispatchStatus;

$dispatches = Motive::dispatches()->list([
    'status' => DispatchStatus::InProgress->value,
]);

foreach ($dispatches as $dispatch) {
    echo "#{$dispatch->externalId}: {$dispatch->status->value}";
    echo "Driver: {$dispatch->driver->firstName} {$dispatch->driver->lastName}";

    foreach ($dispatch->stops as $stop) {
        echo "  - {$stop->type}: {$stop->address}";
    }
}
```

### Create a Dispatch

```php
$dispatch = Motive::dispatches()->create([
    'external_id' => 'ORDER-12345',
    'driver_id' => 123,
    'vehicle_id' => 456,
    'notes' => 'Handle with care - fragile items',
]);

echo "Created dispatch #{$dispatch->id}";
```

### Add Stops to a Dispatch

```php
// Add pickup location
$pickup = Motive::dispatchLocations()->create($dispatch->id, [
    'type' => 'pickup',
    'name' => 'Warehouse A',
    'address' => '123 Industrial Blvd',
    'city' => 'Dallas',
    'state' => 'TX',
    'postal_code' => '75201',
    'scheduled_arrival' => now()->addHours(2)->toIso8601String(),
    'notes' => 'Dock 5',
]);

// Add delivery location
$delivery = Motive::dispatchLocations()->create($dispatch->id, [
    'type' => 'delivery',
    'name' => 'Customer Site',
    'address' => '456 Commerce St',
    'city' => 'Houston',
    'state' => 'TX',
    'postal_code' => '77001',
    'scheduled_arrival' => now()->addHours(8)->toIso8601String(),
]);
```

### Update Dispatch Status

```php
$dispatch = Motive::dispatches()->update($dispatch->id, [
    'status' => DispatchStatus::Completed->value,
]);
```

---

## Assets & Trailers

### List Assets

```php
$assets = Motive::assets()->list();

foreach ($assets as $asset) {
    echo $asset->name;
    echo $asset->assetType; // 'trailer', 'container', etc.
    echo $asset->status->value;
}
```

### Create an Asset

```php
$asset = Motive::assets()->create([
    'name' => 'TRAILER-001',
    'asset_type' => 'trailer',
    'make' => 'Great Dane',
    'model' => 'Everest',
    'year' => 2023,
    'vin' => '1GRAA0622DB500001',
    'license_plate_number' => 'TRL1234',
    'license_plate_state' => 'TX',
]);
```

### Assign Asset to Vehicle

```php
Motive::assets()->assignToVehicle($asset->id, $vehicle->id);

// Unassign
Motive::assets()->unassignFromVehicle($asset->id);
```

---

## Locations & Geofences

### List Locations

```php
$locations = Motive::locations()->list();

foreach ($locations as $location) {
    echo $location->name;
    echo $location->address;
    echo "({$location->latitude}, {$location->longitude})";
}
```

### Create a Location

```php
$location = Motive::locations()->create([
    'name' => 'Dallas Distribution Center',
    'address' => '123 Logistics Way',
    'city' => 'Dallas',
    'state' => 'TX',
    'postal_code' => '75201',
    'latitude' => 32.7767,
    'longitude' => -96.7970,
]);
```

### List Geofences

```php
$geofences = Motive::geofences()->list();

foreach ($geofences as $geofence) {
    echo $geofence->name;
    echo $geofence->type; // 'circle', 'polygon'
    echo "Radius: {$geofence->radius} meters";
}
```

### Create a Circular Geofence

```php
$geofence = Motive::geofences()->create([
    'name' => 'Customer Site A',
    'type' => 'circle',
    'latitude' => 32.7767,
    'longitude' => -96.7970,
    'radius' => 500, // meters
    'notes' => 'Automatic arrival detection',
]);
```

### Create a Polygon Geofence

```php
$geofence = Motive::geofences()->create([
    'name' => 'Warehouse Complex',
    'type' => 'polygon',
    'coordinates' => [
        ['latitude' => 32.7767, 'longitude' => -96.7970],
        ['latitude' => 32.7770, 'longitude' => -96.7965],
        ['latitude' => 32.7765, 'longitude' => -96.7960],
        ['latitude' => 32.7760, 'longitude' => -96.7968],
    ],
]);
```

---

## Inspection Reports (DVIR)

### List Inspection Reports

```php
$reports = Motive::inspectionReports()->list([
    'start_date' => now()->subDays(7)->toDateString(),
    'vehicle_id' => 123,
]);

foreach ($reports as $report) {
    echo $report->type; // 'pre_trip', 'post_trip'
    echo $report->vehicle->number;
    echo $report->driver->firstName;
    echo $report->status; // 'satisfactory', 'defects_found'
    echo $report->createdAt->format('Y-m-d H:i');

    if ($report->defects) {
        foreach ($report->defects as $defect) {
            echo "  Defect: {$defect->area} - {$defect->description}";
        }
    }
}
```

### Get a Specific Report

```php
$report = Motive::inspectionReports()->find($reportId);

// Download the signed PDF
$pdf = Motive::inspectionReports()->downloadPdf($reportId);
file_put_contents('inspection-report.pdf', $pdf);
```

---

## Documents

### List Documents

```php
$documents = Motive::documents()->list([
    'driver_id' => 123,
    'status' => 'pending',
]);

foreach ($documents as $document) {
    echo $document->name;
    echo $document->type;
    echo $document->status->value;
}
```

### Upload a Document

```php
$document = Motive::documents()->upload([
    'driver_id' => 123,
    'name' => 'Bill of Lading - Order 12345',
    'type' => 'bill_of_lading',
    'file' => fopen('/path/to/document.pdf', 'r'),
]);
```

### Download a Document

```php
$content = Motive::documents()->download($document->id);
file_put_contents('downloaded-document.pdf', $content);
```

---

## Messages

### List Messages

```php
$messages = Motive::messages()->list([
    'driver_id' => 123,
]);

foreach ($messages as $message) {
    echo $message->direction; // 'inbound', 'outbound'
    echo $message->body;
    echo $message->sentAt->format('Y-m-d H:i');
}
```

### Send a Message to a Driver

```php
$message = Motive::messages()->send([
    'driver_id' => 123,
    'body' => 'Please call dispatch when you arrive.',
]);
```

### Send a Broadcast Message

```php
$message = Motive::messages()->broadcast([
    'driver_ids' => [123, 456, 789],
    'body' => 'Weather alert: Severe storms expected on I-35 corridor.',
]);
```

---

## Fuel Purchases

### List Fuel Purchases

```php
$purchases = Motive::fuelPurchases()->list([
    'start_date' => now()->subDays(30)->toDateString(),
    'vehicle_id' => 123,
]);

foreach ($purchases as $purchase) {
    echo $purchase->vehicle->number;
    echo $purchase->gallons . ' gallons';
    echo '$' . $purchase->totalAmount;
    echo $purchase->location;
    echo $purchase->purchasedAt->format('Y-m-d');
}
```

### Create a Fuel Purchase

```php
$purchase = Motive::fuelPurchases()->create([
    'vehicle_id' => 123,
    'driver_id' => 456,
    'gallons' => 150.5,
    'price_per_gallon' => 3.459,
    'total_amount' => 520.58,
    'odometer' => 125430,
    'location' => 'Pilot Travel Center, Dallas TX',
    'purchased_at' => now()->toIso8601String(),
]);
```

---

## IFTA Reports

### Generate IFTA Report

```php
$report = Motive::iftaReports()->generate([
    'quarter' => 4,
    'year' => 2024,
]);

foreach ($report->jurisdictions as $jurisdiction) {
    echo $jurisdiction->state;
    echo $jurisdiction->miles . ' miles';
    echo $jurisdiction->gallons . ' gallons';
    echo $jurisdiction->mpg . ' MPG';
}
```

---

## Driver Performance & Safety

### List Driver Performance Events

```php
$events = Motive::driverPerformanceEvents()->list([
    'driver_id' => 123,
    'start_date' => now()->subDays(30)->toDateString(),
    'event_types' => ['harsh_braking', 'speeding', 'rapid_acceleration'],
]);

foreach ($events as $event) {
    echo $event->type;
    echo $event->severity; // 'low', 'medium', 'high'
    echo "Speed: {$event->speed} mph";
    echo "Location: ({$event->latitude}, {$event->longitude})";
    echo $event->occurredAt->format('Y-m-d H:i');
}
```

### Get Driver Scorecard

```php
$scorecard = Motive::scorecard()->forDriver(123, [
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Overall Score: {$scorecard->overallScore}";
echo "Harsh Braking: {$scorecard->harshBrakingScore}";
echo "Speeding: {$scorecard->speedingScore}";
echo "Miles Driven: {$scorecard->totalMiles}";
```

---

## Webhooks

### Register a Webhook

```php
use Motive\Enums\WebhookEvent;

$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::HosViolationDetected->value,
        WebhookEvent::DispatchStatusChanged->value,
    ],
    'secret' => 'your-webhook-secret',
]);
```

### List Webhooks

```php
$webhooks = Motive::webhooks()->list();

foreach ($webhooks as $webhook) {
    echo $webhook->url;
    echo implode(', ', $webhook->events);
    echo $webhook->status; // 'active', 'inactive'
}
```

### Handle Incoming Webhooks

Register the webhook route with signature verification middleware:

```php
// routes/web.php
use Motive\Http\Middleware\VerifyWebhookSignature;

Route::post('/webhooks/motive', [WebhookController::class, 'handle'])
    ->middleware(VerifyWebhookSignature::class);
```

Process webhook payloads:

```php
use Motive\Webhooks\WebhookPayload;
use Motive\Enums\WebhookEvent;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = WebhookPayload::fromRequest($request);

        match ($payload->event) {
            WebhookEvent::VehicleLocationUpdated => $this->handleVehicleLocation($payload),
            WebhookEvent::HosViolationDetected => $this->handleHosViolation($payload),
            WebhookEvent::DispatchStatusChanged => $this->handleDispatchStatus($payload),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    protected function handleVehicleLocation(WebhookPayload $payload): void
    {
        $vehicleId = $payload->data['vehicle_id'];
        $latitude = $payload->data['latitude'];
        $longitude = $payload->data['longitude'];

        // Update your database, broadcast to websockets, etc.
    }
}
```

---

## OAuth Authentication

### Generate Authorization URL

```php
use Motive\Enums\Scope;

$url = Motive::oauth()->authorizationUrl(
    scopes: [
        Scope::VehiclesRead,
        Scope::UsersRead,
        Scope::HosRead,
    ],
    state: 'random-state-string',
);

return redirect($url);
```

### Exchange Code for Tokens

```php
// In your callback controller
$tokens = Motive::oauth()->exchangeCode($request->code);

// Store tokens securely
$user->update([
    'motive_access_token' => $tokens->accessToken,
    'motive_refresh_token' => $tokens->refreshToken,
    'motive_token_expires_at' => $tokens->expiresAt,
]);
```

### Use OAuth Tokens

```php
// With stored tokens
Motive::withOAuth(
    accessToken: $user->motive_access_token,
    refreshToken: $user->motive_refresh_token,
    expiresAt: $user->motive_token_expires_at,
)->vehicles()->list();
```

### Refresh Tokens

```php
$newTokens = Motive::oauth()->refreshToken($user->motive_refresh_token);

$user->update([
    'motive_access_token' => $newTokens->accessToken,
    'motive_refresh_token' => $newTokens->refreshToken,
    'motive_token_expires_at' => $newTokens->expiresAt,
]);
```

### Implementing a Custom Token Store

```php
use Motive\Contracts\TokenStore;

class DatabaseTokenStore implements TokenStore
{
    public function __construct(
        protected User $user
    ) {}

    public function getAccessToken(): ?string
    {
        return $this->user->motive_access_token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->user->motive_refresh_token;
    }

    public function getExpiresAt(): ?CarbonInterface
    {
        return $this->user->motive_token_expires_at;
    }

    public function store(string $accessToken, string $refreshToken, CarbonInterface $expiresAt): void
    {
        $this->user->update([
            'motive_access_token' => $accessToken,
            'motive_refresh_token' => $refreshToken,
            'motive_token_expires_at' => $expiresAt,
        ]);
    }
}

// Usage
Motive::withTokenStore(new DatabaseTokenStore($user))->vehicles()->list();
```

---

## Multi-Tenancy

### Configure Multiple Connections

```php
// config/motive.php
return [
    'default' => 'default',

    'connections' => [
        'default' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_API_KEY'),
        ],
        'company-a' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_COMPANY_A_API_KEY'),
        ],
        'company-b' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_COMPANY_B_API_KEY'),
        ],
    ],
];
```

### Use Different Connections

```php
// Use default connection
$vehicles = Motive::vehicles()->list();

// Use specific connection
$vehiclesA = Motive::connection('company-a')->vehicles()->list();
$vehiclesB = Motive::connection('company-b')->vehicles()->list();
```

### Dynamic Authentication

```php
// Dynamically set API key at runtime
$vehicles = Motive::withApiKey($tenant->motive_api_key)
    ->vehicles()
    ->list();

// Or with OAuth tokens
$vehicles = Motive::withOAuth($tenant->access_token, $tenant->refresh_token)
    ->vehicles()
    ->list();
```

---

## Context Modifiers

### Set Timezone

```php
// All datetime values will be converted to this timezone
$logs = Motive::withTimezone('America/Chicago')
    ->hosLogs()
    ->list();
```

### Use Metric Units

```php
// Distances in kilometers, volumes in liters
$vehicles = Motive::withMetricUnits()
    ->vehicles()
    ->list();
```

### Acting as a User

```php
// Set X-User-Id header for audit trails
$dispatch = Motive::withUserId($currentUser->id)
    ->dispatches()
    ->create([...]);
```

### Chaining Modifiers

```php
$vehicles = Motive::connection('company-a')
    ->withTimezone('America/Los_Angeles')
    ->withMetricUnits()
    ->withUserId($user->id)
    ->vehicles()
    ->list();
```

---

## Error Handling

### Exception Types

```php
use Motive\Exceptions\MotiveException;
use Motive\Exceptions\AuthenticationException;
use Motive\Exceptions\AuthorizationException;
use Motive\Exceptions\ValidationException;
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\RateLimitException;
use Motive\Exceptions\ServerException;

try {
    $vehicle = Motive::vehicles()->find(123);
} catch (NotFoundException $e) {
    // Vehicle not found (404)
    echo "Vehicle not found";
} catch (AuthenticationException $e) {
    // Invalid API key or expired token (401)
    echo "Authentication failed: " . $e->getMessage();
} catch (AuthorizationException $e) {
    // Insufficient permissions (403)
    echo "Not authorized: " . $e->getMessage();
} catch (ValidationException $e) {
    // Invalid request data (422)
    foreach ($e->errors() as $field => $messages) {
        echo "{$field}: " . implode(', ', $messages);
    }
} catch (RateLimitException $e) {
    // Too many requests (429)
    echo "Rate limited. Retry after: " . $e->retryAfter() . " seconds";
} catch (ServerException $e) {
    // Motive server error (5xx)
    echo "Server error: " . $e->getMessage();
} catch (MotiveException $e) {
    // Any other Motive API error
    echo "Error: " . $e->getMessage();
}
```

### Accessing Response Details

```php
try {
    $vehicle = Motive::vehicles()->create([...]);
} catch (MotiveException $e) {
    $statusCode = $e->getCode();
    $response = $e->getResponse(); // Full response object
    $body = $e->getResponseBody(); // Decoded JSON body
}
```

---

## Testing

### Faking the Motive Client

```php
use Motive\Facades\Motive;
use Motive\Data\Vehicle;

public function test_it_syncs_vehicles(): void
{
    Motive::fake([
        'vehicles' => [
            Vehicle::factory()->make(['number' => 'TRUCK-001']),
            Vehicle::factory()->make(['number' => 'TRUCK-002']),
        ],
    ]);

    $this->artisan('sync:vehicles');

    Motive::assertRequested('vehicles.list');
    Motive::assertRequestCount(1);

    $this->assertDatabaseHas('vehicles', ['number' => 'TRUCK-001']);
    $this->assertDatabaseHas('vehicles', ['number' => 'TRUCK-002']);
}
```

### Asserting Specific Requests

```php
Motive::fake();

// Your code that makes API calls
$vehicle = Motive::vehicles()->create([
    'number' => 'NEW-TRUCK',
]);

// Assert the request was made with specific data
Motive::assertRequested('vehicles.create', function ($request) {
    return $request->data('number') === 'NEW-TRUCK';
});
```

### Using Factories

```php
use Motive\Data\Vehicle;
use Motive\Data\User;
use Motive\Data\HosLog;

// Create a single instance
$vehicle = Vehicle::factory()->make();

// Create multiple instances
$vehicles = Vehicle::factory()->count(5)->make();

// With specific attributes
$vehicle = Vehicle::factory()->make([
    'number' => 'CUSTOM-001',
    'status' => VehicleStatus::Active,
]);

// With states
$vehicle = Vehicle::factory()
    ->active()
    ->withCurrentDriver()
    ->make();

// Nested relationships
$user = User::factory()
    ->asDriver()
    ->withVehicle()
    ->make();
```

### Fake Specific Responses

```php
use Motive\Testing\FakeResponse;

Motive::fake([
    'vehicles.list' => FakeResponse::paginated([
        Vehicle::factory()->make(),
    ], total: 100, perPage: 25),

    'vehicles.find' => FakeResponse::json([
        'vehicle' => Vehicle::factory()->make(['id' => 123]),
    ]),

    'vehicles.create' => FakeResponse::error(422, [
        'errors' => ['number' => ['Vehicle number already exists']],
    ]),
]);
```

---

## Advanced Usage

### Raw API Requests

```php
// Make a raw GET request
$response = Motive::get('/v1/custom_endpoint', [
    'param' => 'value',
]);

// Make a raw POST request
$response = Motive::post('/v1/custom_endpoint', [
    'field' => 'value',
]);

// Access response data
$data = $response->json();
$status = $response->status();
$headers = $response->headers();
```

### Macro Extensions

```php
// In a service provider
use Motive\Resources\VehiclesResource;

VehiclesResource::macro('findByLicensePlate', function (string $plate) {
    return $this->list(['license_plate_number' => $plate])->first();
});

// Usage
$vehicle = Motive::vehicles()->findByLicensePlate('ABC1234');
```

### Custom HTTP Client Options

```php
// Temporary timeout override
$vehicles = Motive::withOptions(['timeout' => 60])
    ->vehicles()
    ->list();
```

---

## Available Resources

| Resource | Methods |
|----------|---------|
| `vehicles()` | list, paginate, find, findByNumber, findByExternalId, create, update, delete, currentLocation, locations |
| `users()` | list, paginate, find, findByExternalId, create, update, delete, deactivate, reactivate |
| `assets()` | list, paginate, find, create, update, delete, assignToVehicle, unassignFromVehicle |
| `hosLogs()` | list, paginate, find, create, update, delete, certify |
| `hosAvailability()` | list, forDriver |
| `hosViolations()` | list |
| `dispatches()` | list, paginate, find, create, update, delete |
| `dispatchLocations()` | list, find, create, update, delete |
| `locations()` | list, paginate, find, create, update, delete, findNearest |
| `geofences()` | list, paginate, find, create, update, delete |
| `groups()` | list, paginate, find, create, update, delete, addMember, removeMember |
| `messages()` | list, paginate, find, send, broadcast |
| `documents()` | list, paginate, find, upload, download, delete, updateStatus |
| `inspectionReports()` | list, paginate, find, downloadPdf |
| `fuelPurchases()` | list, paginate, find, create, update, delete |
| `driverPerformanceEvents()` | list, paginate, find |
| `iftaReports()` | generate, list |
| `forms()` | list |
| `formEntries()` | list, find |
| `timecards()` | list, paginate, find, update |
| `utilization()` | forVehicle, forFleet, daily, summary |
| `scorecard()` | forDriver, forFleet |
| `webhooks()` | list, find, create, update, delete, test, logs |
| `companies()` | current |
| `faultCodes()` | list |
| `externalIds()` | set, get, delete |
| `freightVisibility()` | shipments, tracking, eta |
| `motiveCard()` | list, transactions, limits |
| `cameraConnections()` | list |
| `cameraControl()` | requestVideo, getVideo |
| `drivingPeriods()` | list, current, history |
| `vehicleGateways()` | list |
| `reeferActivity()` | list, forVehicle |

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

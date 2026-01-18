## Motive SDK Resources

The SDK provides 31 resources covering all Motive API endpoints.

### Core Resources

#### Vehicles

@verbatim
<code-snippet name="Vehicles resource" lang="php">
use Motive\Facades\Motive;

// List all vehicles
$vehicles = Motive::vehicles()->list();

// Paginate vehicles
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);

// Find by ID
$vehicle = Motive::vehicles()->find(123);

// Find by number
$vehicle = Motive::vehicles()->findByNumber('TRUCK-001');

// Get current location
$location = Motive::vehicles()->currentLocation(123);

// Get location history
$locations = Motive::vehicles()->locations(123, [
    'start_time' => '2024-01-01T00:00:00Z',
    'end_time' => '2024-01-02T00:00:00Z',
]);

// Create vehicle
$vehicle = Motive::vehicles()->create([
    'number' => 'TRUCK-001',
    'make' => 'Freightliner',
    'model' => 'Cascadia',
    'year' => 2024,
    'vin' => '1FUJGBDV5PLEF1234',
]);

// Update vehicle
$vehicle = Motive::vehicles()->update(123, ['status' => 'inactive']);

// Delete vehicle
Motive::vehicles()->delete(123);
</code-snippet>
@endverbatim

#### Users

@verbatim
<code-snippet name="Users resource" lang="php">
// List users
$users = Motive::users()->list();

// Find user
$user = Motive::users()->find(456);

// Create user
$user = Motive::users()->create([
    'email' => 'driver@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'role' => 'driver',
]);

// Deactivate/reactivate user
Motive::users()->deactivate(456);
Motive::users()->reactivate(456);
</code-snippet>
@endverbatim

#### Assets

@verbatim
<code-snippet name="Assets resource" lang="php">
// List assets
$assets = Motive::assets()->list();

// Create asset
$asset = Motive::assets()->create([
    'name' => 'Trailer-001',
    'type' => 'trailer',
]);

// Assign asset to vehicle
Motive::assets()->assignToVehicle(assetId: 789, vehicleId: 123);

// Unassign from vehicle
Motive::assets()->unassignFromVehicle(assetId: 789);
</code-snippet>
@endverbatim

### HOS & Compliance

#### HOS Logs

@verbatim
<code-snippet name="HOS Logs resource" lang="php">
// List HOS logs
$logs = Motive::hosLogs()->list([
    'driver_ids' => [123, 456],
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-07',
]);

// Create HOS log entry
$log = Motive::hosLogs()->create([
    'driver_id' => 123,
    'duty_status' => 'off_duty',
    'start_time' => '2024-01-15T08:00:00Z',
]);

// Certify driver's logs
Motive::hosLogs()->certify(driverId: 123, date: '2024-01-15');
</code-snippet>
@endverbatim

#### HOS Availability

@verbatim
<code-snippet name="HOS Availability resource" lang="php">
// Get driver availability
$availability = Motive::hosAvailability()->forDriver(123);

echo "Drive time remaining: {$availability->driveTimeRemaining} minutes";
echo "Shift time remaining: {$availability->shiftTimeRemaining} minutes";
echo "Cycle time remaining: {$availability->cycleTimeRemaining} minutes";
</code-snippet>
@endverbatim

#### HOS Violations

@verbatim
<code-snippet name="HOS Violations resource" lang="php">
// List violations
$violations = Motive::hosViolations()->list([
    'driver_ids' => [123],
    'start_date' => now()->subDays(30),
]);

foreach ($violations as $violation) {
    echo "{$violation->type->value}: {$violation->duration} minutes";
}
</code-snippet>
@endverbatim

#### Inspection Reports (DVIR)

@verbatim
<code-snippet name="Inspection Reports resource" lang="php">
// List inspection reports
$reports = Motive::inspectionReports()->list();

// Get reports for a driver
$reports = Motive::inspectionReports()->forDriver(123);

// Get reports for a vehicle
$reports = Motive::inspectionReports()->forVehicle(456);
</code-snippet>
@endverbatim

### Dispatch & Location

#### Dispatches

@verbatim
<code-snippet name="Dispatches resource" lang="php">
// List dispatches
$dispatches = Motive::dispatches()->list(['status' => 'pending']);

// Create dispatch
$dispatch = Motive::dispatches()->create([
    'external_id' => 'ORDER-123',
    'driver_id' => 456,
    'vehicle_id' => 789,
]);

// Update dispatch status
$dispatch = Motive::dispatches()->update($dispatch->id, [
    'status' => 'in_progress',
]);
</code-snippet>
@endverbatim

#### Locations

@verbatim
<code-snippet name="Locations resource" lang="php">
// List locations
$locations = Motive::locations()->list();

// Create location
$location = Motive::locations()->create([
    'name' => 'Warehouse A',
    'address' => '123 Main St',
    'city' => 'Chicago',
    'state' => 'IL',
    'latitude' => 41.8781,
    'longitude' => -87.6298,
]);

// Find nearest locations
$nearby = Motive::locations()->findNearest(
    lat: 41.8781,
    lng: -87.6298,
    radius: 5000
);
</code-snippet>
@endverbatim

#### Geofences

@verbatim
<code-snippet name="Geofences resource" lang="php">
// Create circle geofence
$geofence = Motive::geofences()->create([
    'name' => 'Chicago Hub',
    'type' => 'circle',
    'latitude' => 41.8781,
    'longitude' => -87.6298,
    'radius' => 500,
]);

// Create polygon geofence
$geofence = Motive::geofences()->create([
    'name' => 'Warehouse Zone',
    'type' => 'polygon',
    'coordinates' => [
        ['latitude' => 41.8781, 'longitude' => -87.6298],
        ['latitude' => 41.8785, 'longitude' => -87.6295],
        ['latitude' => 41.8780, 'longitude' => -87.6290],
    ],
]);
</code-snippet>
@endverbatim

### Communication & Documents

#### Messages

@verbatim
<code-snippet name="Messages resource" lang="php">
// List messages
$messages = Motive::messages()->list(['driver_id' => 123]);

// Send message to driver
$message = Motive::messages()->send([
    'driver_id' => 123,
    'body' => 'Please report to dispatch.',
]);

// Broadcast to multiple drivers
$messages = Motive::messages()->broadcast([
    'driver_ids' => [123, 456, 789],
    'body' => 'Safety meeting at 3pm.',
]);
</code-snippet>
@endverbatim

#### Documents

@verbatim
<code-snippet name="Documents resource" lang="php">
// List documents
$documents = Motive::documents()->list(['driver_id' => 123]);

// Upload document
$document = Motive::documents()->upload([
    'driver_id' => 123,
    'type' => 'bill_of_lading',
    'file' => base64_encode(file_get_contents('document.pdf')),
]);

// Download document
$content = Motive::documents()->download($document->id);

// Update document status
$document = Motive::documents()->updateStatus($document->id, DocumentStatus::Reviewed);
</code-snippet>
@endverbatim

### Fuel & Reporting

#### Fuel Purchases

@verbatim
<code-snippet name="Fuel Purchases resource" lang="php">
// List fuel purchases
$purchases = Motive::fuelPurchases()->list([
    'vehicle_id' => 123,
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
]);

// Create fuel purchase
$purchase = Motive::fuelPurchases()->create([
    'vehicle_id' => 123,
    'driver_id' => 456,
    'gallons' => 150.5,
    'total_cost' => 525.75,
    'location' => 'Pilot #123',
]);
</code-snippet>
@endverbatim

#### IFTA Reports

@verbatim
<code-snippet name="IFTA Reports resource" lang="php">
// Generate IFTA report
$report = Motive::iftaReports()->generate([
    'quarter' => 'Q1',
    'year' => 2024,
]);

// List IFTA reports
$reports = Motive::iftaReports()->list();
</code-snippet>
@endverbatim

#### Driver Performance

@verbatim
<code-snippet name="Driver Performance resource" lang="php">
// List performance events
$events = Motive::driverPerformanceEvents()->list([
    'driver_id' => 123,
    'event_types' => ['harsh_braking', 'speeding'],
]);
</code-snippet>
@endverbatim

#### Scorecards

@verbatim
<code-snippet name="Scorecards resource" lang="php">
// Get driver scorecard
$scorecard = Motive::scorecards()->forDriver(123, [
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
]);

// Get fleet scorecard
$scorecard = Motive::scorecards()->forFleet([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
]);
</code-snippet>
@endverbatim

### OAuth & Webhooks

#### OAuth Flow

@verbatim
<code-snippet name="OAuth authentication" lang="php">
use Motive\Enums\Scope;

// Generate authorization URL
$url = Motive::oauth()->authorizationUrl(
    scopes: [Scope::VehiclesRead, Scope::UsersRead],
    state: 'random-state',
);

// Exchange code for tokens
$tokens = Motive::oauth()->exchangeCode($code);

// Use OAuth tokens
Motive::withOAuth(
    accessToken: $tokens->accessToken,
    refreshToken: $tokens->refreshToken,
    expiresAt: $tokens->expiresAt
)->vehicles()->list();

// Refresh token
$newTokens = Motive::oauth()->refreshToken($tokens->refreshToken);
</code-snippet>
@endverbatim

#### Webhooks

@verbatim
<code-snippet name="Webhooks resource" lang="php">
// Create webhook
$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => ['vehicle.location.updated', 'hos.violation.detected'],
]);

// List webhooks
$webhooks = Motive::webhooks()->list();

// Test webhook
Motive::webhooks()->test($webhook->id);

// Get webhook logs
$logs = Motive::webhooks()->logs($webhook->id);
</code-snippet>
@endverbatim

### All Available Resources

| Resource | Method | Description |
|----------|--------|-------------|
| `vehicles()` | CRUD + location | Vehicle management |
| `users()` | CRUD + activate | User management |
| `assets()` | CRUD + assign | Asset tracking |
| `companies()` | read | Company info |
| `hosLogs()` | CRUD + certify | HOS log entries |
| `hosAvailability()` | read | Driver availability |
| `hosViolations()` | read | HOS violations |
| `inspectionReports()` | read | DVIR reports |
| `faultCodes()` | read | Vehicle fault codes |
| `dispatches()` | CRUD | Dispatch management |
| `locations()` | CRUD + nearest | Location management |
| `geofences()` | CRUD | Geofence management |
| `groups()` | CRUD + members | Group management |
| `messages()` | read + send | Driver messaging |
| `documents()` | CRUD + download | Document management |
| `fuelPurchases()` | CRUD | Fuel tracking |
| `iftaReports()` | read + generate | IFTA reports |
| `driverPerformanceEvents()` | read | Safety events |
| `scorecards()` | read | Performance scores |
| `utilization()` | read | Vehicle utilization |
| `timecards()` | read + update | Timecard management |
| `forms()` | CRUD | Form templates |
| `formEntries()` | read + create | Form submissions |
| `drivingPeriods()` | read | Driving period data |
| `motiveCards()` | read | Fuel card management |
| `freightVisibility()` | read | Shipment tracking |
| `cameraConnections()` | read | Camera devices |
| `cameraControl()` | request video | Video requests |
| `externalIds()` | CRUD | External ID mapping |
| `vehicleGateways()` | read | ELD devices |
| `reeferActivity()` | read | Reefer monitoring |
| `webhooks()` | CRUD + test | Webhook management |

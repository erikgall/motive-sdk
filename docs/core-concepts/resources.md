# Resources

Resources are the primary way to interact with the Motive API. Each resource class provides methods for a specific API endpoint.

## Accessing Resources

Resources are accessed through the `MotiveManager` (or `Motive` facade):

```php
use Motive\Facades\Motive;

// Get resource instances
$vehiclesResource = Motive::vehicles();
$usersResource = Motive::users();
$hosLogsResource = Motive::hosLogs();
```

## Available Resources

### Fleet Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Vehicles | `vehicles()` | Fleet vehicle management |
| Assets | `assets()` | Trailers and equipment |
| Vehicle Gateways | `vehicleGateways()` | ELD device information |
| Fault Codes | `faultCodes()` | Vehicle diagnostic codes |

### Driver Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Users | `users()` | Driver and staff management |
| Driving Periods | `drivingPeriods()` | Driver activity periods |
| Scorecard | `scorecard()` | Driver safety scores |

### Compliance Resources

| Resource | Method | Description |
|----------|--------|-------------|
| HOS Logs | `hosLogs()` | Hours of Service logs |
| HOS Availability | `hosAvailability()` | Remaining drive time |
| HOS Violations | `hosViolations()` | HOS rule violations |
| Inspection Reports | `inspectionReports()` | DVIR reports |
| IFTA Reports | `iftaReports()` | Fuel tax reports |

### Dispatch Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Dispatches | `dispatches()` | Load and route management |
| Locations | `locations()` | Named locations |
| Geofences | `geofences()` | Geographic boundaries |

### Communication Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Messages | `messages()` | Driver messaging |
| Documents | `documents()` | Document management |

### Safety Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Driver Performance Events | `driverPerformanceEvents()` | Safety events |
| Camera Connections | `cameraConnections()` | Dashboard cameras |
| Camera Control | `cameraControl()` | Video retrieval |

### Operations Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Groups | `groups()` | Organizational groups |
| Companies | `companies()` | Company information |
| Forms | `forms()` | Custom form templates |
| Form Entries | `formEntries()` | Submitted form data |
| Timecards | `timecards()` | Time tracking |
| Utilization | `utilization()` | Vehicle utilization metrics |

### Financial Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Fuel Purchases | `fuelPurchases()` | Fuel transaction records |
| Motive Card | `motiveCard()` | Fuel card management |

### Integration Resources

| Resource | Method | Description |
|----------|--------|-------------|
| Webhooks | `webhooks()` | Webhook subscriptions |
| External IDs | `externalIds()` | External system mappings |
| Freight Visibility | `freightVisibility()` | Shipment tracking |
| Reefer Activity | `reeferActivity()` | Refrigerated trailer data |

## Common Methods

Most resources support these standard methods:

### list()

Returns a lazy collection that automatically paginates:

```php
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo $vehicle->number;
}

// With filters
$vehicles = Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);
```

### paginate()

Returns a single page with pagination metadata:

```php
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);

echo "Total: {$page->total()}";
echo "Current page: {$page->currentPage()}";
echo "Last page: {$page->lastPage()}";

foreach ($page->items() as $vehicle) {
    echo $vehicle->number;
}
```

### find()

Retrieves a single resource by ID:

```php
$vehicle = Motive::vehicles()->find(123);
echo $vehicle->number;
```

### create()

Creates a new resource:

```php
$vehicle = Motive::vehicles()->create([
    'number' => 'TRUCK-001',
    'make' => 'Freightliner',
    'model' => 'Cascadia',
    'year' => 2024,
]);
```

### update()

Updates an existing resource:

```php
$vehicle = Motive::vehicles()->update(123, [
    'number' => 'TRUCK-001-UPDATED',
]);
```

### delete()

Deletes a resource:

```php
$deleted = Motive::vehicles()->delete(123);
```

## Specialized Methods

Many resources have additional methods beyond CRUD:

```php
// Vehicles
$location = Motive::vehicles()->currentLocation(123);
$history = Motive::vehicles()->locations(123, ['start_date' => '2024-01-01']);

// Users
Motive::users()->deactivate(456);
Motive::users()->reactivate(456);

// Assets
Motive::assets()->assignToVehicle($assetId, $vehicleId);
Motive::assets()->unassignFromVehicle($assetId);

// HOS Availability
$availability = Motive::hosAvailability()->forDriver(123);

// Messages
$message = Motive::messages()->send(['driver_id' => 123, 'body' => 'Hello']);
Motive::messages()->broadcast(['driver_ids' => [1, 2, 3], 'body' => 'Alert']);

// Utilization
$report = Motive::utilization()->forVehicle(123, [...]);
$fleetReport = Motive::utilization()->forFleet([...]);
```

## Chaining Context Modifiers

Resources inherit context modifiers from the manager:

```php
// All requests use timezone and metric units
$vehicles = Motive::withTimezone('America/Chicago')
    ->withMetricUnits()
    ->vehicles()
    ->list();
```

## Resource Traits

Resources use traits to compose functionality:

| Trait | Methods |
|-------|---------|
| `HasCrudOperations` | list, paginate, find, create, update, delete |
| `HasListOperation` | list, paginate |
| `HasFindOperation` | find |
| `HasCreateOperation` | create |
| `HasUpdateOperation` | update |
| `HasDeleteOperation` | delete |

This allows resources to expose only the operations supported by their API endpoint.

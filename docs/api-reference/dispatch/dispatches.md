# Dispatches

The Dispatches resource manages load and route assignments for drivers.

## Access

```php
use Motive\Facades\Motive;

$dispatches = Motive::dispatches();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List dispatches |
| `paginate($page, $perPage, $params)` | Get paginated dispatches |
| `find($id)` | Find dispatch by ID |
| `create($data)` | Create a dispatch |
| `update($id, $data)` | Update a dispatch |
| `delete($id)` | Delete a dispatch |

## List Dispatches

```php
use Motive\Enums\DispatchStatus;

$dispatches = Motive::dispatches()->list([
    'status' => DispatchStatus::InProgress->value,
]);

foreach ($dispatches as $dispatch) {
    echo "#{$dispatch->externalId}: {$dispatch->status->value}\n";
    echo "Driver: {$dispatch->driver->firstName} {$dispatch->driver->lastName}\n";

    foreach ($dispatch->stops as $stop) {
        echo "  - {$stop->type}: {$stop->address}\n";
    }
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status |
| `driver_id` | int | Filter by driver |
| `vehicle_id` | int | Filter by vehicle |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `per_page` | int | Items per page |

## Find a Dispatch

```php
$dispatch = Motive::dispatches()->find($dispatchId);

echo "External ID: {$dispatch->externalId}";
echo "Status: {$dispatch->status->value}";
echo "Driver: {$dispatch->driver->firstName}";
```

## Create a Dispatch

```php
$dispatch = Motive::dispatches()->create([
    'external_id' => 'ORDER-12345',
    'driver_id' => 123,
    'vehicle_id' => 456,
    'notes' => 'Handle with care - fragile items',
]);

echo "Created dispatch #{$dispatch->id}";
```

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `external_id` | string | No | Your system's order ID |
| `driver_id` | int | No | Assigned driver ID |
| `vehicle_id` | int | No | Assigned vehicle ID |
| `notes` | string | No | Dispatch notes |
| `scheduled_start` | string | No | Scheduled start time |
| `scheduled_end` | string | No | Scheduled end time |

## Update a Dispatch

```php
use Motive\Enums\DispatchStatus;

$dispatch = Motive::dispatches()->update($dispatchId, [
    'status' => DispatchStatus::Completed->value,
    'notes' => 'Delivered successfully',
]);
```

## Delete a Dispatch

```php
$deleted = Motive::dispatches()->delete($dispatchId);
```

## Dispatch DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Dispatch ID |
| `externalId` | string\|null | External reference ID |
| `status` | DispatchStatus | Current status |
| `driverId` | int\|null | Assigned driver ID |
| `driver` | User\|null | Driver details |
| `vehicleId` | int\|null | Assigned vehicle ID |
| `vehicle` | Vehicle\|null | Vehicle details |
| `stops` | array\|null | Dispatch stops |
| `notes` | string\|null | Notes |
| `scheduledStart` | CarbonImmutable\|null | Scheduled start |
| `scheduledEnd` | CarbonImmutable\|null | Scheduled end |
| `startedAt` | CarbonImmutable\|null | Actual start |
| `completedAt` | CarbonImmutable\|null | Completion time |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## DispatchStatus Enum

| Value | Description |
|-------|-------------|
| `pending` | Not yet started |
| `in_progress` | Currently active |
| `completed` | Successfully completed |
| `cancelled` | Cancelled |

## Adding Stops

Use the Dispatch Locations resource to add stops:

```php
// Add pickup
Motive::dispatchLocations()->create($dispatch->id, [
    'type' => 'pickup',
    'name' => 'Warehouse A',
    'address' => '123 Industrial Blvd',
    'city' => 'Dallas',
    'state' => 'TX',
    'postal_code' => '75201',
    'scheduled_arrival' => now()->addHours(2)->toIso8601String(),
]);

// Add delivery
Motive::dispatchLocations()->create($dispatch->id, [
    'type' => 'delivery',
    'name' => 'Customer Site',
    'address' => '456 Commerce St',
    'city' => 'Houston',
    'state' => 'TX',
    'postal_code' => '77001',
    'scheduled_arrival' => now()->addHours(8)->toIso8601String(),
]);
```

## Use Cases

### Active Dispatch Dashboard

```php
$active = Motive::dispatches()->list([
    'status' => DispatchStatus::InProgress->value,
]);

foreach ($active as $dispatch) {
    $vehicle = $dispatch->vehicle;
    $location = Motive::vehicles()->currentLocation($vehicle->id);

    echo "{$dispatch->externalId}\n";
    echo "Location: {$location->latitude}, {$location->longitude}\n";
}
```

## Related

- [Locations](locations.md)
- [Geofences](geofences.md)
- [DispatchStatus Enum](../../enums/status-enums.md)

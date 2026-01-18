# Vehicles

The Vehicles resource provides access to your fleet's vehicle data, including location tracking and management operations.

## Access

```php
use Motive\Facades\Motive;

$vehicles = Motive::vehicles();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List all vehicles |
| `paginate($page, $perPage, $params)` | Get paginated vehicles |
| `find($id)` | Find vehicle by ID |
| `findByNumber($number)` | Find vehicle by number |
| `findByExternalId($externalId)` | Find vehicle by external ID |
| `create($data)` | Create a new vehicle |
| `update($id, $data)` | Update a vehicle |
| `delete($id)` | Delete a vehicle |
| `currentLocation($id)` | Get current location |
| `locations($id, $params)` | Get location history |

## List Vehicles

```php
// List all vehicles with lazy pagination
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}

// With filters
$vehicles = Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status: `active`, `inactive` |
| `per_page` | int | Items per page (max 100) |
| `page_token` | string | Pagination token |

## Paginate Vehicles

```php
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);

echo "Showing {$page->count()} of {$page->total()} vehicles";
echo "Page {$page->currentPage()} of {$page->lastPage()}";

foreach ($page->items() as $vehicle) {
    echo $vehicle->number;
}
```

## Find a Vehicle

```php
// By ID
$vehicle = Motive::vehicles()->find(123);

// By vehicle number
$vehicle = Motive::vehicles()->findByNumber('TRUCK-001');

// By external ID
$vehicle = Motive::vehicles()->findByExternalId('ext-123');
```

## Create a Vehicle

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

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `number` | string | Yes | Unique vehicle number |
| `make` | string | No | Vehicle make |
| `model` | string | No | Vehicle model |
| `year` | int | No | Vehicle year |
| `vin` | string | No | Vehicle identification number |
| `license_plate_number` | string | No | License plate number |
| `license_plate_state` | string | No | License plate state |
| `external_id` | string | No | External system ID |

## Update a Vehicle

```php
$vehicle = Motive::vehicles()->update(123, [
    'number' => 'TRUCK-042-UPDATED',
    'license_plate_number' => 'XYZ9876',
]);
```

## Delete a Vehicle

```php
$deleted = Motive::vehicles()->delete(123);

if ($deleted) {
    echo "Vehicle deleted successfully";
}
```

## Get Current Location

```php
$location = Motive::vehicles()->currentLocation(123);

echo "Lat: {$location->latitude}, Lng: {$location->longitude}";
echo "Speed: {$location->speed} mph";
echo "Heading: {$location->bearing}";
echo "Updated: {$location->locatedAt->diffForHumans()}";
```

## Get Location History

```php
$locations = Motive::vehicles()->locations(123, [
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($locations as $location) {
    echo "{$location->locatedAt}: ({$location->latitude}, {$location->longitude})";
}
```

### Location History Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `start_date` | string | Yes | Start date (YYYY-MM-DD) |
| `end_date` | string | Yes | End date (YYYY-MM-DD) |

## Vehicle DTO

The `Vehicle` DTO includes these properties:

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Vehicle ID |
| `number` | string | Vehicle number |
| `make` | string\|null | Make |
| `model` | string\|null | Model |
| `year` | int\|null | Year |
| `vin` | string\|null | VIN |
| `licensePlateNumber` | string\|null | License plate |
| `licensePlateState` | string\|null | License plate state |
| `status` | VehicleStatus | Current status |
| `externalId` | string\|null | External ID |
| `createdAt` | CarbonImmutable\|null | Created timestamp |
| `updatedAt` | CarbonImmutable\|null | Updated timestamp |

## VehicleLocation DTO

| Property | Type | Description |
|----------|------|-------------|
| `latitude` | float | Latitude |
| `longitude` | float | Longitude |
| `speed` | float\|null | Speed (mph or km/h) |
| `bearing` | int\|null | Heading in degrees |
| `locatedAt` | CarbonImmutable | Location timestamp |

## Related

- [Vehicle DTO Reference](../../dto-reference/fleet.md)
- [VehicleStatus Enum](../../enums/status-enums.md)

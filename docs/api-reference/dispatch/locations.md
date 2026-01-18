# Locations

The Locations resource manages named locations for dispatch and geofencing.

## Access

```php
use Motive\Facades\Motive;

$locations = Motive::locations();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List locations |
| `paginate($page, $perPage, $params)` | Get paginated locations |
| `find($id)` | Find location by ID |
| `create($data)` | Create a location |
| `update($id, $data)` | Update a location |
| `delete($id)` | Delete a location |
| `findNearest($lat, $lng, $params)` | Find nearest locations |

## List Locations

```php
$locations = Motive::locations()->list();

foreach ($locations as $location) {
    echo "{$location->name}\n";
    echo "Address: {$location->address}\n";
    echo "Coordinates: ({$location->latitude}, {$location->longitude})\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `name` | string | Search by name |
| `per_page` | int | Items per page |

## Find a Location

```php
$location = Motive::locations()->find($locationId);

echo $location->name;
echo $location->address;
```

## Create a Location

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

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Location name |
| `address` | string | No | Street address |
| `city` | string | No | City |
| `state` | string | No | State |
| `postal_code` | string | No | Postal/ZIP code |
| `country` | string | No | Country code |
| `latitude` | float | No | Latitude |
| `longitude` | float | No | Longitude |
| `notes` | string | No | Additional notes |
| `external_id` | string | No | External system ID |

## Update a Location

```php
$location = Motive::locations()->update($locationId, [
    'name' => 'Dallas DC - Main',
    'notes' => 'Updated entrance instructions',
]);
```

## Delete a Location

```php
$deleted = Motive::locations()->delete($locationId);
```

## Find Nearest Locations

```php
$nearest = Motive::locations()->findNearest(
    lat: 32.7767,
    lng: -96.7970,
    params: ['radius' => 50] // miles
);

foreach ($nearest as $location) {
    echo "{$location->name}: {$location->distance} miles\n";
}
```

## Location DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Location ID |
| `name` | string | Location name |
| `address` | string\|null | Street address |
| `city` | string\|null | City |
| `state` | string\|null | State |
| `postalCode` | string\|null | Postal code |
| `country` | string\|null | Country |
| `latitude` | float\|null | Latitude |
| `longitude` | float\|null | Longitude |
| `notes` | string\|null | Notes |
| `externalId` | string\|null | External ID |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## Use Cases

### Location Directory

```php
$locations = Motive::locations()->list();

$byState = collect($locations)->groupBy('state');

foreach ($byState as $state => $stateLocations) {
    echo "{$state}: " . count($stateLocations) . " locations\n";
}
```

### Dispatch Location Lookup

```php
// Find nearby delivery points
$vehicleLocation = Motive::vehicles()->currentLocation(123);

$nearby = Motive::locations()->findNearest(
    $vehicleLocation->latitude,
    $vehicleLocation->longitude,
    ['radius' => 25]
);

echo "Nearby locations:\n";
foreach ($nearby as $location) {
    echo "- {$location->name}\n";
}
```

## Related

- [Dispatches](dispatches.md)
- [Geofences](geofences.md)

# Geofences

The Geofences resource manages geographic boundaries for location-based alerts.

## Access

```php
use Motive\Facades\Motive;

$geofences = Motive::geofences();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List geofences |
| `paginate($page, $perPage, $params)` | Get paginated geofences |
| `find($id)` | Find geofence by ID |
| `create($data)` | Create a geofence |
| `update($id, $data)` | Update a geofence |
| `delete($id)` | Delete a geofence |

## List Geofences

```php
$geofences = Motive::geofences()->list();

foreach ($geofences as $geofence) {
    echo "{$geofence->name}\n";
    echo "Type: {$geofence->type}\n";

    if ($geofence->type === 'circle') {
        echo "Center: ({$geofence->latitude}, {$geofence->longitude})\n";
        echo "Radius: {$geofence->radius} meters\n";
    }
}
```

## Find a Geofence

```php
$geofence = Motive::geofences()->find($geofenceId);

echo $geofence->name;
echo $geofence->type;
```

## Create a Circular Geofence

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

## Create a Polygon Geofence

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

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Geofence name |
| `type` | string | Yes | `circle` or `polygon` |
| `latitude` | float | For circle | Center latitude |
| `longitude` | float | For circle | Center longitude |
| `radius` | int | For circle | Radius in meters |
| `coordinates` | array | For polygon | Polygon vertices |
| `notes` | string | No | Additional notes |
| `external_id` | string | No | External system ID |

## Update a Geofence

```php
$geofence = Motive::geofences()->update($geofenceId, [
    'name' => 'Customer Site A - Updated',
    'radius' => 750,
]);
```

## Delete a Geofence

```php
$deleted = Motive::geofences()->delete($geofenceId);
```

## Geofence DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Geofence ID |
| `name` | string | Geofence name |
| `type` | GeofenceType | Circle or polygon |
| `latitude` | float\|null | Center latitude (circle) |
| `longitude` | float\|null | Center longitude (circle) |
| `radius` | int\|null | Radius in meters (circle) |
| `coordinates` | array\|null | Polygon vertices |
| `notes` | string\|null | Notes |
| `externalId` | string\|null | External ID |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## GeofenceCoordinate DTO

| Property | Type | Description |
|----------|------|-------------|
| `latitude` | float | Latitude |
| `longitude` | float | Longitude |
| `order` | int\|null | Vertex order |

## GeofenceType Enum

| Value | Description |
|-------|-------------|
| `circle` | Circular geofence |
| `polygon` | Polygon geofence |

## Use Cases

### Customer Site Setup

```php
// Create geofences for all customer locations
$customers = Customer::all();

foreach ($customers as $customer) {
    Motive::geofences()->create([
        'name' => $customer->name,
        'type' => 'circle',
        'latitude' => $customer->latitude,
        'longitude' => $customer->longitude,
        'radius' => 200,
        'external_id' => "customer-{$customer->id}",
    ]);
}
```

### Geofence Management Dashboard

```php
$geofences = Motive::geofences()->list();

$stats = [
    'total' => 0,
    'circle' => 0,
    'polygon' => 0,
];

foreach ($geofences as $geofence) {
    $stats['total']++;
    $stats[$geofence->type]++;
}

echo "Total geofences: {$stats['total']}\n";
echo "Circles: {$stats['circle']}\n";
echo "Polygons: {$stats['polygon']}\n";
```

## Related

- [Locations](locations.md)
- [Dispatches](dispatches.md)
- [GeofenceType Enum](../../enums/type-enums.md)

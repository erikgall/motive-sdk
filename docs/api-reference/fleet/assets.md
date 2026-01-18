# Assets

The Assets resource manages trailers, containers, and other equipment in your fleet.

## Access

```php
use Motive\Facades\Motive;

$assets = Motive::assets();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List all assets |
| `paginate($page, $perPage, $params)` | Get paginated assets |
| `find($id)` | Find asset by ID |
| `create($data)` | Create a new asset |
| `update($id, $data)` | Update an asset |
| `delete($id)` | Delete an asset |
| `assignToVehicle($assetId, $vehicleId)` | Assign asset to vehicle |
| `unassignFromVehicle($assetId)` | Unassign asset from vehicle |

## List Assets

```php
$assets = Motive::assets()->list();

foreach ($assets as $asset) {
    echo "{$asset->name}: {$asset->assetType}\n";
    echo "Status: {$asset->status->value}\n";
}

// With filters
$trailers = Motive::assets()->list([
    'asset_type' => 'trailer',
    'status' => 'active',
]);
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `asset_type` | string | Filter by type: `trailer`, `container`, etc. |
| `status` | string | Filter by status |
| `per_page` | int | Items per page |

## Find an Asset

```php
$asset = Motive::assets()->find(456);

echo $asset->name;
echo $asset->assetType;
echo $asset->status->value;
```

## Create an Asset

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

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Asset name/number |
| `asset_type` | string | Yes | Type: `trailer`, `container`, etc. |
| `make` | string | No | Manufacturer |
| `model` | string | No | Model |
| `year` | int | No | Year |
| `vin` | string | No | VIN |
| `license_plate_number` | string | No | License plate |
| `license_plate_state` | string | No | License plate state |
| `external_id` | string | No | External system ID |

## Update an Asset

```php
$asset = Motive::assets()->update(456, [
    'name' => 'TRAILER-001-UPDATED',
    'license_plate_number' => 'TRL5678',
]);
```

## Delete an Asset

```php
$deleted = Motive::assets()->delete(456);
```

## Assign Asset to Vehicle

Link an asset (trailer) to a vehicle (tractor):

```php
Motive::assets()->assignToVehicle($assetId, $vehicleId);

// Example
Motive::assets()->assignToVehicle(456, 123);
echo "Trailer assigned to truck";
```

## Unassign Asset from Vehicle

Remove the asset-vehicle association:

```php
Motive::assets()->unassignFromVehicle($assetId);

// Example
Motive::assets()->unassignFromVehicle(456);
echo "Trailer unassigned";
```

## Asset DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Asset ID |
| `name` | string | Asset name |
| `assetType` | string | Type (trailer, container, etc.) |
| `make` | string\|null | Manufacturer |
| `model` | string\|null | Model |
| `year` | int\|null | Year |
| `vin` | string\|null | VIN |
| `licensePlateNumber` | string\|null | License plate |
| `licensePlateState` | string\|null | License plate state |
| `status` | AssetStatus | Current status |
| `vehicleId` | int\|null | Assigned vehicle ID |
| `externalId` | string\|null | External ID |
| `createdAt` | CarbonImmutable\|null | Created timestamp |
| `updatedAt` | CarbonImmutable\|null | Updated timestamp |

## Related

- [Asset DTO Reference](../../dto-reference/fleet.md)
- [AssetStatus Enum](../../enums/status-enums.md)
- [AssetType Enum](../../enums/type-enums.md)

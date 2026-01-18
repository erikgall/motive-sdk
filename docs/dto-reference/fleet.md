# Fleet DTOs

## Vehicle

Represents a fleet vehicle.

```php
use Motive\Data\Vehicle;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Vehicle ID |
| `number` | `string` | Vehicle number |
| `make` | `string\|null` | Vehicle make |
| `model` | `string\|null` | Vehicle model |
| `year` | `int\|null` | Model year |
| `vin` | `string\|null` | VIN |
| `licensePlateNumber` | `string\|null` | License plate number |
| `licensePlateState` | `string\|null` | License plate state |
| `status` | `VehicleStatus` | Vehicle status |
| `externalId` | `string\|null` | External system ID |
| `fuelType` | `string\|null` | Fuel type |
| `currentDriverId` | `int\|null` | Current driver ID |
| `currentDriver` | `User\|null` | Current driver |
| `group` | `Group\|null` | Assigned group |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |
| `updatedAt` | `CarbonImmutable\|null` | Updated timestamp |

### Example

```php
$vehicle = Motive::vehicles()->find(123);

echo $vehicle->number;           // "TRUCK-001"
echo $vehicle->make;             // "Freightliner"
echo $vehicle->status->value;    // "active"
echo $vehicle->createdAt;        // CarbonImmutable instance
```

---

## Asset

Represents a trailer or other equipment.

```php
use Motive\Data\Asset;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Asset ID |
| `name` | `string` | Asset name |
| `assetType` | `string` | Asset type |
| `make` | `string\|null` | Manufacturer |
| `model` | `string\|null` | Model |
| `year` | `int\|null` | Year |
| `vin` | `string\|null` | VIN |
| `licensePlateNumber` | `string\|null` | License plate |
| `licensePlateState` | `string\|null` | License plate state |
| `status` | `AssetStatus` | Current status |
| `vehicleId` | `int\|null` | Assigned vehicle ID |
| `externalId` | `string\|null` | External ID |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |
| `updatedAt` | `CarbonImmutable\|null` | Updated timestamp |

---

## VehicleGateway

Represents an ELD device installed in a vehicle.

```php
use Motive\Data\VehicleGateway;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Gateway ID |
| `vehicleId` | `int` | Vehicle ID |
| `serialNumber` | `string` | Serial number |
| `firmwareVersion` | `string\|null` | Firmware version |
| `model` | `string\|null` | Device model |
| `status` | `string\|null` | Connection status |
| `lastConnectedAt` | `CarbonImmutable\|null` | Last connection |

---

## VehicleLocation

Represents a vehicle's position at a point in time.

```php
use Motive\Data\VehicleLocation;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `vehicleId` | `int\|null` | Vehicle ID |
| `latitude` | `float` | Latitude |
| `longitude` | `float` | Longitude |
| `speed` | `float\|null` | Speed (mph or km/h) |
| `bearing` | `int\|null` | Heading in degrees |
| `altitude` | `float\|null` | Altitude |
| `odometer` | `int\|null` | Odometer reading |
| `locatedAt` | `CarbonImmutable` | Location timestamp |

### Example

```php
$location = Motive::vehicles()->currentLocation(123);

echo "({$location->latitude}, {$location->longitude})";
echo "Speed: {$location->speed} mph";
echo "Last updated: {$location->locatedAt->diffForHumans()}";
```

---

## FaultCode

Represents a vehicle diagnostic trouble code.

```php
use Motive\Data\FaultCode;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Record ID |
| `vehicleId` | `int` | Vehicle ID |
| `code` | `string` | DTC code |
| `description` | `string\|null` | Description |
| `severity` | `string\|null` | Severity level |
| `source` | `string\|null` | Code source |
| `detectedAt` | `CarbonImmutable` | Detection time |
| `clearedAt` | `CarbonImmutable\|null` | Cleared time |

## Related

- [Vehicles Resource](../api-reference/fleet/vehicles.md)
- [Assets Resource](../api-reference/fleet/assets.md)
- [VehicleStatus Enum](../enums/status-enums.md)

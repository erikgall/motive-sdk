# Dispatch DTOs

## Dispatch

Represents a dispatch/load assignment.

```php
use Motive\Data\Dispatch;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Dispatch ID |
| `externalId` | `string\|null` | External reference |
| `status` | `DispatchStatus` | Current status |
| `driverId` | `int\|null` | Assigned driver |
| `driver` | `User\|null` | Driver details |
| `vehicleId` | `int\|null` | Assigned vehicle |
| `vehicle` | `Vehicle\|null` | Vehicle details |
| `stops` | `array\|null` | Dispatch stops |
| `notes` | `string\|null` | Notes |
| `scheduledStart` | `CarbonImmutable\|null` | Scheduled start |
| `scheduledEnd` | `CarbonImmutable\|null` | Scheduled end |
| `startedAt` | `CarbonImmutable\|null` | Actual start |
| `completedAt` | `CarbonImmutable\|null` | Completion time |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## DispatchStop

Represents a pickup or delivery stop.

```php
use Motive\Data\DispatchStop;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int\|null` | Stop ID |
| `type` | `StopType` | Pickup or delivery |
| `name` | `string\|null` | Location name |
| `address` | `string\|null` | Street address |
| `city` | `string\|null` | City |
| `state` | `string\|null` | State |
| `postalCode` | `string\|null` | Postal code |
| `latitude` | `float\|null` | Latitude |
| `longitude` | `float\|null` | Longitude |
| `scheduledArrival` | `CarbonImmutable\|null` | Scheduled time |
| `actualArrival` | `CarbonImmutable\|null` | Actual arrival |
| `actualDeparture` | `CarbonImmutable\|null` | Actual departure |
| `notes` | `string\|null` | Notes |
| `order` | `int\|null` | Stop order |

---

## Location

Represents a named location.

```php
use Motive\Data\Location;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Location ID |
| `name` | `string` | Location name |
| `address` | `string\|null` | Street address |
| `city` | `string\|null` | City |
| `state` | `string\|null` | State |
| `postalCode` | `string\|null` | Postal code |
| `country` | `string\|null` | Country |
| `latitude` | `float\|null` | Latitude |
| `longitude` | `float\|null` | Longitude |
| `notes` | `string\|null` | Notes |
| `externalId` | `string\|null` | External ID |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## Geofence

Represents a geographic boundary.

```php
use Motive\Data\Geofence;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Geofence ID |
| `name` | `string` | Geofence name |
| `type` | `GeofenceType` | Circle or polygon |
| `latitude` | `float\|null` | Center latitude (circle) |
| `longitude` | `float\|null` | Center longitude (circle) |
| `radius` | `int\|null` | Radius in meters (circle) |
| `coordinates` | `array\|null` | Polygon vertices |
| `notes` | `string\|null` | Notes |
| `externalId` | `string\|null` | External ID |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## GeofenceCoordinate

Represents a polygon vertex.

```php
use Motive\Data\GeofenceCoordinate;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `latitude` | `float` | Latitude |
| `longitude` | `float` | Longitude |
| `order` | `int\|null` | Vertex order |

## Related

- [Dispatches Resource](../api-reference/dispatch/dispatches.md)
- [DispatchStatus Enum](../enums/status-enums.md)
- [GeofenceType Enum](../enums/type-enums.md)

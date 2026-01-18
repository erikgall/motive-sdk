# Safety DTOs

## DriverPerformanceEvent

Represents a safety-related driving event.

```php
use Motive\Data\DriverPerformanceEvent;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Event ID |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `vehicleId` | `int\|null` | Vehicle ID |
| `vehicle` | `Vehicle\|null` | Vehicle details |
| `type` | `PerformanceEventType` | Event type |
| `severity` | `EventSeverity` | Severity level |
| `speed` | `float\|null` | Speed at event |
| `speedLimit` | `float\|null` | Posted speed limit |
| `latitude` | `float` | Latitude |
| `longitude` | `float` | Longitude |
| `location` | `string\|null` | Location description |
| `occurredAt` | `CarbonImmutable` | Event timestamp |

### Example

```php
$events = Motive::driverPerformanceEvents()->list([
    'severity' => 'high',
]);

foreach ($events as $event) {
    echo "{$event->type->value}: {$event->severity->value}\n";
    echo "Speed: {$event->speed} mph (limit: {$event->speedLimit})\n";
}
```

---

## CameraConnection

Represents a dashboard camera installation.

```php
use Motive\Data\CameraConnection;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Connection ID |
| `vehicleId` | `int` | Vehicle ID |
| `vehicle` | `Vehicle\|null` | Vehicle details |
| `type` | `CameraType` | Camera type |
| `serialNumber` | `string\|null` | Serial number |
| `status` | `string` | Connection status |
| `firmwareVersion` | `string\|null` | Firmware version |
| `lastConnectedAt` | `CarbonImmutable\|null` | Last connection |

---

## VideoRequest

Represents a video retrieval request.

```php
use Motive\Data\VideoRequest;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Request ID |
| `vehicleId` | `int` | Vehicle ID |
| `status` | `VideoStatus` | Request status |
| `cameraType` | `CameraType\|null` | Camera type |
| `startTime` | `CarbonImmutable` | Requested start |
| `endTime` | `CarbonImmutable` | Requested end |
| `createdAt` | `CarbonImmutable` | Request timestamp |

---

## Video

Represents a video recording.

```php
use Motive\Data\Video;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Video ID |
| `requestId` | `int` | Request ID |
| `status` | `VideoStatus` | Video status |
| `url` | `string\|null` | Download URL |
| `duration` | `int\|null` | Duration in seconds |
| `fileSize` | `int\|null` | Size in bytes |
| `expiresAt` | `CarbonImmutable\|null` | URL expiration |

## Related

- [Driver Performance Resource](../api-reference/safety/driver-performance.md)
- [Camera Control Resource](../api-reference/safety/camera-control.md)
- [PerformanceEventType Enum](../enums/type-enums.md)
- [EventSeverity Enum](../enums/type-enums.md)
- [VideoStatus Enum](../enums/status-enums.md)

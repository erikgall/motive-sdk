# Camera Control

The Camera Control resource enables video retrieval from dashboard cameras.

## Access

```php
use Motive\Facades\Motive;

$cameraControl = Motive::cameraControl();
```

## Methods

| Method | Description |
|--------|-------------|
| `requestVideo($params)` | Request a video clip |
| `getVideo($requestId)` | Get video request status |

## Request a Video

```php
$request = Motive::cameraControl()->requestVideo([
    'vehicle_id' => 123,
    'start_time' => now()->subMinutes(5)->toIso8601String(),
    'end_time' => now()->toIso8601String(),
    'camera_type' => 'road_facing',
]);

echo "Video request ID: {$request->id}";
echo "Status: {$request->status}";
```

### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `vehicle_id` | int | Yes | Vehicle ID |
| `start_time` | string | Yes | Start time (ISO 8601) |
| `end_time` | string | Yes | End time (ISO 8601) |
| `camera_type` | string | No | `road_facing`, `driver_facing`, or `dual` |
| `event_id` | int | No | Associated performance event ID |

## Get Video Status

```php
$video = Motive::cameraControl()->getVideo($requestId);

echo "Status: {$video->status}\n";

if ($video->status === VideoStatus::Ready) {
    echo "Download URL: {$video->url}\n";
    echo "Expires: {$video->expiresAt}\n";
}
```

## VideoRequest DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Request ID |
| `vehicleId` | int | Vehicle ID |
| `status` | VideoStatus | Request status |
| `cameraType` | CameraType\|null | Camera type |
| `startTime` | CarbonImmutable | Requested start |
| `endTime` | CarbonImmutable | Requested end |
| `createdAt` | CarbonImmutable | Request timestamp |

## Video DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Video ID |
| `requestId` | int | Original request ID |
| `status` | VideoStatus | Video status |
| `url` | string\|null | Download URL |
| `duration` | int\|null | Duration in seconds |
| `fileSize` | int\|null | File size in bytes |
| `expiresAt` | CarbonImmutable\|null | URL expiration |

## VideoStatus Enum

| Value | Description |
|-------|-------------|
| `pending` | Request submitted |
| `processing` | Video being retrieved |
| `ready` | Video ready for download |
| `failed` | Request failed |
| `expired` | Download URL expired |

## Use Cases

### Incident Video Retrieval

```php
// Get video for a safety event
$event = Motive::driverPerformanceEvents()->find($eventId);

$request = Motive::cameraControl()->requestVideo([
    'vehicle_id' => $event->vehicleId,
    'start_time' => $event->occurredAt->subSeconds(30)->toIso8601String(),
    'end_time' => $event->occurredAt->addSeconds(30)->toIso8601String(),
    'camera_type' => 'dual',
    'event_id' => $event->id,
]);

// Poll for completion
do {
    sleep(5);
    $video = Motive::cameraControl()->getVideo($request->id);
} while ($video->status === VideoStatus::Processing);

if ($video->status === VideoStatus::Ready) {
    // Download or provide link
    echo "Video ready: {$video->url}";
}
```

### Bulk Video Request

```php
$events = Motive::driverPerformanceEvents()->list([
    'severity' => 'high',
    'start_date' => now()->subDays(1)->toDateString(),
]);

$requests = [];
foreach ($events as $event) {
    $requests[] = Motive::cameraControl()->requestVideo([
        'vehicle_id' => $event->vehicleId,
        'start_time' => $event->occurredAt->subSeconds(15)->toIso8601String(),
        'end_time' => $event->occurredAt->addSeconds(15)->toIso8601String(),
        'event_id' => $event->id,
    ]);
}

echo count($requests) . " video requests submitted";
```

## Related

- [Camera Connections](camera-connections.md)
- [Driver Performance](driver-performance.md)

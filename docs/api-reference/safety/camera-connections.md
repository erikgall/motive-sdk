# Camera Connections

The Camera Connections resource provides information about dashboard cameras installed in your fleet.

## Access

```php
use Motive\Facades\Motive;

$cameras = Motive::cameraConnections();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List camera connections |

## List Camera Connections

```php
$cameras = Motive::cameraConnections()->list();

foreach ($cameras as $camera) {
    echo "Vehicle: {$camera->vehicleId}\n";
    echo "Type: {$camera->type}\n";
    echo "Status: {$camera->status}\n";
    echo "Serial: {$camera->serialNumber}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `vehicle_id` | int | Filter by vehicle |
| `status` | string | Filter by connection status |
| `per_page` | int | Items per page |

## CameraConnection DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Connection ID |
| `vehicleId` | int | Vehicle ID |
| `vehicle` | Vehicle\|null | Vehicle details |
| `type` | CameraType | Camera type |
| `serialNumber` | string\|null | Device serial number |
| `status` | string | Connection status |
| `firmwareVersion` | string\|null | Firmware version |
| `lastConnectedAt` | CarbonImmutable\|null | Last connection time |

## CameraType Enum

| Value | Description |
|-------|-------------|
| `road_facing` | Forward-facing camera |
| `driver_facing` | Inward-facing camera |
| `dual` | Both road and driver facing |

## Use Cases

### Camera Status Dashboard

```php
$cameras = Motive::cameraConnections()->list();

$stats = [
    'total' => 0,
    'connected' => 0,
    'disconnected' => 0,
];

foreach ($cameras as $camera) {
    $stats['total']++;

    if ($camera->lastConnectedAt?->diffInHours(now()) < 24) {
        $stats['connected']++;
    } else {
        $stats['disconnected']++;
    }
}

echo "Camera Status:\n";
echo "Total: {$stats['total']}\n";
echo "Connected: {$stats['connected']}\n";
echo "Disconnected: {$stats['disconnected']}\n";
```

### Camera Coverage Report

```php
$vehicles = Motive::vehicles()->list(['status' => 'active'])->all();
$cameras = Motive::cameraConnections()->list();

$vehiclesWithCameras = collect($cameras)->pluck('vehicleId')->unique();
$vehiclesWithoutCameras = [];

foreach ($vehicles as $vehicle) {
    if (! $vehiclesWithCameras->contains($vehicle->id)) {
        $vehiclesWithoutCameras[] = $vehicle;
    }
}

echo count($vehiclesWithoutCameras) . " vehicles without cameras\n";
```

## Related

- [Camera Control](camera-control.md)
- [Vehicles](../fleet/vehicles.md)

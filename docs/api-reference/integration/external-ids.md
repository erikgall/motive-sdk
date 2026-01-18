# External IDs

The External IDs resource maps Motive resources to identifiers in your external systems.

## Access

```php
use Motive\Facades\Motive;

$externalIds = Motive::externalIds();
```

## Methods

| Method | Description |
|--------|-------------|
| `set($resourceType, $resourceId, $externalId)` | Set external ID |
| `get($resourceType, $externalId)` | Get resource by external ID |
| `delete($resourceType, $resourceId)` | Remove external ID |

## Set External ID

Link a Motive resource to your system's identifier:

```php
// Link a vehicle to your TMS
Motive::externalIds()->set('vehicle', 123, 'TMS-VEH-456');

// Link a driver to your HR system
Motive::externalIds()->set('user', 789, 'HR-EMP-123');

// Link a dispatch to your order system
Motive::externalIds()->set('dispatch', 456, 'ORDER-2024-789');
```

### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `resourceType` | string | Type: `vehicle`, `user`, `dispatch`, `asset`, `location`, `geofence` |
| `resourceId` | int | Motive resource ID |
| `externalId` | string | Your external identifier |

## Get by External ID

Look up a Motive resource using your external ID:

```php
// Find vehicle by TMS ID
$vehicle = Motive::externalIds()->get('vehicle', 'TMS-VEH-456');
echo "Motive Vehicle ID: {$vehicle->id}";

// Find user by HR ID
$user = Motive::externalIds()->get('user', 'HR-EMP-123');
echo "Motive User ID: {$user->id}";
```

## Delete External ID

Remove the external ID mapping:

```php
Motive::externalIds()->delete('vehicle', 123);
```

## ExternalId DTO

| Property | Type | Description |
|----------|------|-------------|
| `resourceType` | string | Resource type |
| `resourceId` | int | Motive resource ID |
| `externalId` | string | External identifier |
| `createdAt` | CarbonImmutable\|null | Mapping created |

## Supported Resource Types

| Type | Description |
|------|-------------|
| `vehicle` | Fleet vehicles |
| `user` | Users/drivers |
| `dispatch` | Dispatches |
| `asset` | Assets/trailers |
| `location` | Locations |
| `geofence` | Geofences |

## Use Cases

### TMS Integration

```php
class TmsIntegration
{
    public function syncVehicle(TmsVehicle $tmsVehicle): void
    {
        // Check if already linked
        $existing = Motive::externalIds()->get('vehicle', $tmsVehicle->id);

        if ($existing) {
            // Update existing vehicle
            Motive::vehicles()->update($existing->id, [
                'number' => $tmsVehicle->unit_number,
            ]);
        } else {
            // Create new vehicle and link
            $vehicle = Motive::vehicles()->create([
                'number' => $tmsVehicle->unit_number,
                'vin' => $tmsVehicle->vin,
            ]);

            Motive::externalIds()->set('vehicle', $vehicle->id, $tmsVehicle->id);
        }
    }
}
```

### Order Dispatch Sync

```php
class OrderDispatchService
{
    public function createDispatchForOrder(Order $order): void
    {
        // Create dispatch in Motive
        $dispatch = Motive::dispatches()->create([
            'external_id' => $order->order_number,
            'driver_id' => $this->getDriverId($order->assigned_driver),
            'vehicle_id' => $this->getVehicleId($order->assigned_truck),
        ]);

        // Link to order
        Motive::externalIds()->set('dispatch', $dispatch->id, $order->id);

        // Update order with Motive reference
        $order->update(['motive_dispatch_id' => $dispatch->id]);
    }

    protected function getDriverId(string $hrId): int
    {
        $user = Motive::externalIds()->get('user', $hrId);
        return $user->id;
    }

    protected function getVehicleId(string $tmsId): int
    {
        $vehicle = Motive::externalIds()->get('vehicle', $tmsId);
        return $vehicle->id;
    }
}
```

### Batch ID Mapping

```php
// Sync all vehicles from TMS
$tmsVehicles = TmsApi::getVehicles();

foreach ($tmsVehicles as $tmsVehicle) {
    // Find or create in Motive
    $vehicle = Motive::vehicles()->findByExternalId($tmsVehicle->id);

    if (! $vehicle) {
        $vehicle = Motive::vehicles()->create([
            'number' => $tmsVehicle->unit_number,
            'vin' => $tmsVehicle->vin,
        ]);

        Motive::externalIds()->set('vehicle', $vehicle->id, $tmsVehicle->id);
    }
}
```

## Related

- [Vehicles](../fleet/vehicles.md)
- [Users](../drivers/users.md)
- [Dispatches](../dispatch/dispatches.md)

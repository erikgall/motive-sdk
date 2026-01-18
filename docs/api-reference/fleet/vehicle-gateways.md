# Vehicle Gateways

The Vehicle Gateways resource provides information about ELD devices installed in your vehicles.

## Access

```php
use Motive\Facades\Motive;

$gateways = Motive::vehicleGateways();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List all vehicle gateways |

## List Vehicle Gateways

```php
$gateways = Motive::vehicleGateways()->list();

foreach ($gateways as $gateway) {
    echo "Vehicle: {$gateway->vehicleId}\n";
    echo "Serial: {$gateway->serialNumber}\n";
    echo "Firmware: {$gateway->firmwareVersion}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `vehicle_id` | int | Filter by vehicle ID |
| `per_page` | int | Items per page |

## VehicleGateway DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Gateway ID |
| `vehicleId` | int | Associated vehicle ID |
| `serialNumber` | string | Device serial number |
| `firmwareVersion` | string\|null | Current firmware version |
| `model` | string\|null | Device model |
| `status` | string\|null | Connection status |
| `lastConnectedAt` | CarbonImmutable\|null | Last connection time |

## Use Cases

### Audit ELD Compliance

```php
$gateways = Motive::vehicleGateways()->list();

foreach ($gateways as $gateway) {
    if ($gateway->lastConnectedAt?->diffInHours(now()) > 24) {
        Log::warning("Gateway disconnected", [
            'vehicle_id' => $gateway->vehicleId,
            'serial' => $gateway->serialNumber,
            'last_connected' => $gateway->lastConnectedAt,
        ]);
    }
}
```

### Track Firmware Versions

```php
$gateways = Motive::vehicleGateways()->list();
$outdated = [];

foreach ($gateways as $gateway) {
    if (version_compare($gateway->firmwareVersion, '2.5.0', '<')) {
        $outdated[] = $gateway;
    }
}

echo count($outdated) . " gateways need firmware updates";
```

## Related

- [Vehicles](vehicles.md)

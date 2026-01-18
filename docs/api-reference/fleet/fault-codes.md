# Fault Codes

The Fault Codes resource retrieves diagnostic trouble codes (DTCs) from your fleet vehicles.

## Access

```php
use Motive\Facades\Motive;

$faultCodes = Motive::faultCodes();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List all fault codes |

## List Fault Codes

```php
$faultCodes = Motive::faultCodes()->list([
    'vehicle_id' => 123,
    'start_date' => now()->subDays(30)->toDateString(),
]);

foreach ($faultCodes as $code) {
    echo "Vehicle: {$code->vehicleId}\n";
    echo "Code: {$code->code}\n";
    echo "Description: {$code->description}\n";
    echo "Severity: {$code->severity}\n";
    echo "Detected: {$code->detectedAt}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `vehicle_id` | int | Filter by vehicle ID |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `severity` | string | Filter by severity |
| `per_page` | int | Items per page |

## FaultCode DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Fault code record ID |
| `vehicleId` | int | Vehicle ID |
| `code` | string | DTC code |
| `description` | string\|null | Human-readable description |
| `severity` | string\|null | Severity level |
| `source` | string\|null | Code source (engine, transmission, etc.) |
| `detectedAt` | CarbonImmutable | When the code was detected |
| `clearedAt` | CarbonImmutable\|null | When the code was cleared |

## Use Cases

### Maintenance Alerts

```php
$faultCodes = Motive::faultCodes()->list([
    'start_date' => now()->subDays(1)->toDateString(),
]);

$critical = [];
foreach ($faultCodes as $code) {
    if ($code->severity === 'critical') {
        $critical[] = $code;

        // Send notification
        Notification::send(
            $maintenanceTeam,
            new CriticalFaultCodeNotification($code)
        );
    }
}
```

### Fleet Health Dashboard

```php
$faultCodes = Motive::faultCodes()->list([
    'start_date' => now()->subDays(7)->toDateString(),
]);

$byVehicle = collect($faultCodes)->groupBy('vehicleId');

foreach ($byVehicle as $vehicleId => $codes) {
    echo "Vehicle {$vehicleId}: " . count($codes) . " fault codes\n";
}
```

### Preventive Maintenance

```php
// Find vehicles with repeated fault codes
$faultCodes = Motive::faultCodes()->list([
    'start_date' => now()->subDays(30)->toDateString(),
]);

$repeatedCodes = collect($faultCodes)
    ->groupBy(fn ($code) => "{$code->vehicleId}:{$code->code}")
    ->filter(fn ($group) => $group->count() > 2);

foreach ($repeatedCodes as $key => $codes) {
    [$vehicleId, $code] = explode(':', $key);
    echo "Vehicle {$vehicleId} has repeated code {$code}\n";
}
```

## Related

- [Vehicles](vehicles.md)

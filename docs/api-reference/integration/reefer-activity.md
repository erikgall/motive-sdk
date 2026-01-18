# Reefer Activity

The Reefer Activity resource monitors refrigerated trailer temperature and operation data.

## Access

```php
use Motive\Facades\Motive;

$reeferActivity = Motive::reeferActivity();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List reefer activity records |
| `forVehicle($vehicleId, $params)` | Get activity for a vehicle |

## List Reefer Activity

```php
$activity = Motive::reeferActivity()->list([
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($activity as $record) {
    echo "Vehicle: {$record->vehicleId}\n";
    echo "Temperature: {$record->temperature}°F\n";
    echo "Setpoint: {$record->setpoint}°F\n";
    echo "Mode: {$record->mode}\n";
    echo "Time: {$record->recordedAt}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `vehicle_id` | int | Filter by vehicle |
| `asset_id` | int | Filter by asset |
| `per_page` | int | Items per page |

## Get Activity for Vehicle

```php
$activity = Motive::reeferActivity()->forVehicle(123, [
    'start_date' => now()->subHours(24)->toIso8601String(),
    'end_date' => now()->toIso8601String(),
]);

foreach ($activity as $record) {
    echo "{$record->recordedAt->format('H:i')}: {$record->temperature}°F\n";
}
```

## ReeferActivity DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Record ID |
| `vehicleId` | int\|null | Vehicle ID |
| `assetId` | int\|null | Asset/trailer ID |
| `temperature` | float | Current temperature |
| `setpoint` | float\|null | Target temperature |
| `mode` | string\|null | Operating mode |
| `fuelLevel` | float\|null | Fuel level percentage |
| `engineHours` | float\|null | Engine hours |
| `alarms` | array\|null | Active alarms |
| `recordedAt` | CarbonImmutable | Recording time |

## Use Cases

### Temperature Monitoring Dashboard

```php
$vehicles = Motive::vehicles()->list(['asset_type' => 'reefer'])->all();

$dashboard = [];
foreach ($vehicles as $vehicle) {
    $activity = Motive::reeferActivity()->forVehicle($vehicle->id, [
        'start_date' => now()->subHours(1)->toIso8601String(),
        'end_date' => now()->toIso8601String(),
    ]);

    $latest = collect($activity)->sortByDesc('recordedAt')->first();

    if ($latest) {
        $dashboard[] = [
            'vehicle' => $vehicle->number,
            'temperature' => $latest->temperature,
            'setpoint' => $latest->setpoint,
            'in_range' => abs($latest->temperature - $latest->setpoint) <= 5,
        ];
    }
}
```

### Temperature Excursion Alerts

```php
$activity = Motive::reeferActivity()->list([
    'start_date' => now()->subHours(1)->toIso8601String(),
]);

$excursions = [];
foreach ($activity as $record) {
    if ($record->setpoint && abs($record->temperature - $record->setpoint) > 10) {
        $excursions[] = $record;

        Log::warning('Temperature excursion detected', [
            'vehicle_id' => $record->vehicleId,
            'temperature' => $record->temperature,
            'setpoint' => $record->setpoint,
            'deviation' => abs($record->temperature - $record->setpoint),
        ]);
    }
}
```

### Temperature History Report

```php
$activity = Motive::reeferActivity()->forVehicle($vehicleId, [
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

$temps = collect($activity)->pluck('temperature');

echo "Temperature Statistics:\n";
echo "Min: {$temps->min()}°F\n";
echo "Max: {$temps->max()}°F\n";
echo "Average: " . round($temps->average(), 1) . "°F\n";

// Find time out of range
$outOfRange = collect($activity)->filter(function ($record) {
    return $record->setpoint &&
           abs($record->temperature - $record->setpoint) > 5;
})->count();

$totalRecords = count($activity);
$compliance = (($totalRecords - $outOfRange) / $totalRecords) * 100;

echo "Compliance Rate: " . round($compliance, 1) . "%\n";
```

### Fuel Level Monitoring

```php
$activity = Motive::reeferActivity()->list([
    'start_date' => now()->subHours(1)->toIso8601String(),
]);

$lowFuel = [];
foreach ($activity as $record) {
    if ($record->fuelLevel !== null && $record->fuelLevel < 20) {
        $lowFuel[$record->vehicleId] = $record->fuelLevel;
    }
}

if (! empty($lowFuel)) {
    echo "Vehicles with low reefer fuel:\n";
    foreach ($lowFuel as $vehicleId => $level) {
        echo "- Vehicle {$vehicleId}: {$level}%\n";
    }
}
```

## Related

- [Vehicles](../fleet/vehicles.md)
- [Assets](../fleet/assets.md)

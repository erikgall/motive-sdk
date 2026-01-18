# Driving Periods

The Driving Periods resource tracks driver activity periods and driving sessions.

## Access

```php
use Motive\Facades\Motive;

$drivingPeriods = Motive::drivingPeriods();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List driving periods |
| `current($driverId)` | Get current driving period |
| `history($driverId, $params)` | Get driving period history |

## List Driving Periods

```php
$periods = Motive::drivingPeriods()->list([
    'driver_ids' => [123, 456],
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($periods as $period) {
    echo "Driver: {$period->driverId}\n";
    echo "Start: {$period->startTime}\n";
    echo "End: {$period->endTime}\n";
    echo "Duration: {$period->duration} minutes\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_ids` | array | Filter by driver IDs |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `per_page` | int | Items per page |

## Get Current Driving Period

```php
$current = Motive::drivingPeriods()->current(123);

if ($current) {
    echo "Currently driving\n";
    echo "Started: {$current->startTime}\n";
    echo "Duration: {$current->duration} minutes\n";
} else {
    echo "Not currently driving";
}
```

## Get Driving Period History

```php
$history = Motive::drivingPeriods()->history(123, [
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

$totalMinutes = 0;
foreach ($history as $period) {
    $totalMinutes += $period->duration;
}

echo "Total driving time: " . round($totalMinutes / 60, 1) . " hours";
```

## DrivingPeriod DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Period ID |
| `driverId` | int | Driver ID |
| `vehicleId` | int\|null | Vehicle ID |
| `startTime` | CarbonImmutable | Period start time |
| `endTime` | CarbonImmutable\|null | Period end time |
| `duration` | int | Duration in minutes |
| `distance` | float\|null | Distance traveled |
| `startLocation` | string\|null | Starting location |
| `endLocation` | string\|null | Ending location |

## Use Cases

### Driver Activity Report

```php
$periods = Motive::drivingPeriods()->list([
    'start_date' => now()->startOfWeek()->toDateString(),
    'end_date' => now()->toDateString(),
]);

$byDriver = collect($periods)->groupBy('driverId');

foreach ($byDriver as $driverId => $driverPeriods) {
    $totalHours = collect($driverPeriods)->sum('duration') / 60;
    echo "Driver {$driverId}: {$totalHours} hours\n";
}
```

### Real-Time Driving Status

```php
$drivers = Motive::users()->list(['role' => 'driver']);

foreach ($drivers as $driver) {
    $current = Motive::drivingPeriods()->current($driver->id);

    if ($current) {
        echo "{$driver->firstName} is driving ({$current->duration} min)\n";
    }
}
```

## Related

- [Users](users.md)
- [HOS Logs](../compliance/hos-logs.md)

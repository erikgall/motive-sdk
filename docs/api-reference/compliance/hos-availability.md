# HOS Availability

The HOS Availability resource provides real-time information about drivers' remaining Hours of Service.

## Access

```php
use Motive\Facades\Motive;

$hosAvailability = Motive::hosAvailability();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List availability for multiple drivers |
| `forDriver($driverId)` | Get availability for a specific driver |

## Get Availability for a Driver

```php
$availability = Motive::hosAvailability()->forDriver(123);

echo "Drive time remaining: {$availability->driveTimeRemaining} minutes\n";
echo "Shift time remaining: {$availability->shiftTimeRemaining} minutes\n";
echo "Cycle time remaining: {$availability->cycleTimeRemaining} minutes\n";
echo "Break time required in: {$availability->breakTimeRequired} minutes\n";

// Check if driver can drive
if ($availability->driveTimeRemaining > 0 && $availability->shiftTimeRemaining > 0) {
    echo "Driver is available to drive";
}
```

## List Availability for Multiple Drivers

```php
$availabilities = Motive::hosAvailability()->list([
    'driver_ids' => [123, 456, 789],
]);

foreach ($availabilities as $availability) {
    echo "{$availability->driver->firstName}: ";
    echo "{$availability->driveTimeRemaining} min remaining\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_ids` | array | Filter by driver IDs |

## HosAvailability DTO

| Property | Type | Description |
|----------|------|-------------|
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `driveTimeRemaining` | int | Remaining drive minutes |
| `shiftTimeRemaining` | int | Remaining shift minutes |
| `cycleTimeRemaining` | int | Remaining cycle minutes |
| `breakTimeRequired` | int\|null | Minutes until break required |
| `currentStatus` | DutyStatus\|null | Current duty status |
| `lastUpdated` | CarbonImmutable\|null | Last update time |

## Understanding HOS Limits

### Drive Time (11-Hour Rule)
- Maximum 11 hours of driving within a 14-hour shift
- `driveTimeRemaining` shows minutes left

### Shift Time (14-Hour Rule)
- Maximum 14-hour on-duty window
- `shiftTimeRemaining` shows minutes left in window

### Cycle Time (70-Hour Rule)
- Maximum 70 hours over 8 consecutive days
- `cycleTimeRemaining` shows minutes left in cycle

### Break Requirement (30-Minute Rule)
- 30-minute break required before 8 hours of driving
- `breakTimeRequired` shows minutes until break is needed

## Use Cases

### Dispatch Planning

```php
$drivers = Motive::users()->list(['role' => 'driver']);
$availableForLoad = [];

foreach ($drivers as $driver) {
    $availability = Motive::hosAvailability()->forDriver($driver->id);

    // Need at least 4 hours of drive time
    if ($availability->driveTimeRemaining >= 240) {
        $availableForLoad[] = [
            'driver' => $driver,
            'availability' => $availability,
        ];
    }
}

echo count($availableForLoad) . " drivers available for dispatch";
```

### Real-Time Dashboard

```php
$availabilities = Motive::hosAvailability()->list([
    'driver_ids' => $activeDriverIds,
]);

$dashboard = [];
foreach ($availabilities as $availability) {
    $dashboard[] = [
        'driver_id' => $availability->driverId,
        'drive_hours' => round($availability->driveTimeRemaining / 60, 1),
        'shift_hours' => round($availability->shiftTimeRemaining / 60, 1),
        'cycle_hours' => round($availability->cycleTimeRemaining / 60, 1),
        'needs_break' => $availability->breakTimeRequired !== null &&
                         $availability->breakTimeRequired < 30,
        'status' => $availability->currentStatus?->value,
    ];
}
```

### Violation Prevention

```php
$availability = Motive::hosAvailability()->forDriver(123);

$warnings = [];

if ($availability->driveTimeRemaining < 60) {
    $warnings[] = 'Less than 1 hour of drive time remaining';
}

if ($availability->shiftTimeRemaining < 60) {
    $warnings[] = 'Shift ending in less than 1 hour';
}

if ($availability->breakTimeRequired !== null && $availability->breakTimeRequired < 30) {
    $warnings[] = '30-minute break required soon';
}

if (! empty($warnings)) {
    Notification::send($driver, new HosWarningNotification($warnings));
}
```

## Related

- [HOS Logs](hos-logs.md)
- [HOS Violations](hos-violations.md)
- [DutyStatus Enum](../../enums/hos-enums.md)

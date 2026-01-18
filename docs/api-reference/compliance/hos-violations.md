# HOS Violations

The HOS Violations resource tracks Hours of Service rule violations.

## Access

```php
use Motive\Facades\Motive;

$hosViolations = Motive::hosViolations();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List HOS violations |

## List Violations

```php
$violations = Motive::hosViolations()->list([
    'driver_ids' => [123],
    'start_date' => now()->subDays(30)->toDateString(),
]);

foreach ($violations as $violation) {
    echo "Type: {$violation->type}\n";
    echo "Driver: {$violation->driverId}\n";
    echo "Start: {$violation->startTime->format('Y-m-d H:i')}\n";
    echo "Duration: {$violation->duration} minutes\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_ids` | array | Filter by driver IDs |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `violation_type` | string | Filter by type |
| `per_page` | int | Items per page |

## HosViolation DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Violation ID |
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `type` | HosViolationType | Violation type |
| `startTime` | CarbonImmutable | Violation start |
| `endTime` | CarbonImmutable\|null | Violation end |
| `duration` | int\|null | Duration in minutes |
| `description` | string\|null | Description |

## HosViolationType Enum

| Value | Description |
|-------|-------------|
| `11_hour` | Exceeded 11-hour driving limit |
| `14_hour` | Exceeded 14-hour shift limit |
| `30_minute_break` | Missing required 30-minute break |
| `70_hour` | Exceeded 70-hour cycle limit |
| `rest_break` | Insufficient rest period |

## Use Cases

### Compliance Report

```php
$violations = Motive::hosViolations()->list([
    'start_date' => now()->subMonth()->toDateString(),
    'end_date' => now()->toDateString(),
]);

$byType = [];
foreach ($violations as $violation) {
    $type = $violation->type->value;
    $byType[$type] = ($byType[$type] ?? 0) + 1;
}

echo "Violations by type:\n";
foreach ($byType as $type => $count) {
    echo "- {$type}: {$count}\n";
}
```

### Driver Violation History

```php
$violations = Motive::hosViolations()->list([
    'driver_ids' => [123],
    'start_date' => now()->subYear()->toDateString(),
]);

$monthly = [];
foreach ($violations as $violation) {
    $month = $violation->startTime->format('Y-m');
    $monthly[$month] = ($monthly[$month] ?? 0) + 1;
}

echo "Violations per month:\n";
foreach ($monthly as $month => $count) {
    echo "{$month}: {$count}\n";
}
```

### Real-Time Alerts

```php
$violations = Motive::hosViolations()->list([
    'start_date' => now()->toDateString(),
]);

foreach ($violations as $violation) {
    $driver = Motive::users()->find($violation->driverId);

    Notification::send($safetyManager, new HosViolationAlert([
        'driver' => $driver->firstName . ' ' . $driver->lastName,
        'type' => $violation->type->value,
        'time' => $violation->startTime->format('H:i'),
    ]));
}
```

### Fleet Safety Dashboard

```php
$violations = Motive::hosViolations()->list([
    'start_date' => now()->subDays(7)->toDateString(),
]);

$drivers = Motive::users()->list(['role' => 'driver'])->all();

$violationRate = count($violations) / count($drivers);

return [
    'total_violations' => count($violations),
    'driver_count' => count($drivers),
    'violations_per_driver' => round($violationRate, 2),
    'most_common' => collect($violations)
        ->groupBy(fn ($v) => $v->type->value)
        ->map->count()
        ->sortDesc()
        ->keys()
        ->first(),
];
```

## Related

- [HOS Logs](hos-logs.md)
- [HOS Availability](hos-availability.md)
- [HosViolationType Enum](../../enums/hos-enums.md)

# Driver Performance

The Driver Performance Events resource tracks safety-related driving events.

## Access

```php
use Motive\Facades\Motive;

$events = Motive::driverPerformanceEvents();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List performance events |
| `paginate($page, $perPage, $params)` | Get paginated events |
| `find($id)` | Find event by ID |

## List Performance Events

```php
$events = Motive::driverPerformanceEvents()->list([
    'driver_id' => 123,
    'start_date' => now()->subDays(30)->toDateString(),
    'event_types' => ['harsh_braking', 'speeding', 'rapid_acceleration'],
]);

foreach ($events as $event) {
    echo "Type: {$event->type}\n";
    echo "Severity: {$event->severity}\n";
    echo "Speed: {$event->speed} mph\n";
    echo "Location: ({$event->latitude}, {$event->longitude})\n";
    echo "Time: {$event->occurredAt->format('Y-m-d H:i')}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_id` | int | Filter by driver |
| `vehicle_id` | int | Filter by vehicle |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `event_types` | array | Filter by event types |
| `severity` | string | Filter by severity |
| `per_page` | int | Items per page |

## Find an Event

```php
$event = Motive::driverPerformanceEvents()->find($eventId);

echo $event->type;
echo $event->severity;
echo $event->speed;
```

## DriverPerformanceEvent DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Event ID |
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `vehicleId` | int\|null | Vehicle ID |
| `vehicle` | Vehicle\|null | Vehicle details |
| `type` | PerformanceEventType | Event type |
| `severity` | EventSeverity | Severity level |
| `speed` | float\|null | Speed at event |
| `speedLimit` | float\|null | Posted speed limit |
| `latitude` | float | Latitude |
| `longitude` | float | Longitude |
| `location` | string\|null | Location description |
| `occurredAt` | CarbonImmutable | Event timestamp |

## PerformanceEventType Enum

| Value | Description |
|-------|-------------|
| `harsh_braking` | Hard braking event |
| `rapid_acceleration` | Rapid acceleration |
| `speeding` | Exceeding speed limit |
| `cornering` | Hard cornering |
| `seatbelt` | Seatbelt unbuckled |
| `distraction` | Driver distraction |
| `fatigue` | Drowsiness detected |

## EventSeverity Enum

| Value | Description |
|-------|-------------|
| `low` | Minor event |
| `medium` | Moderate event |
| `high` | Severe event |

## Use Cases

### Safety Dashboard

```php
$events = Motive::driverPerformanceEvents()->list([
    'start_date' => now()->subDays(7)->toDateString(),
]);

$byType = [];
$bySeverity = [];

foreach ($events as $event) {
    $type = $event->type->value;
    $severity = $event->severity->value;

    $byType[$type] = ($byType[$type] ?? 0) + 1;
    $bySeverity[$severity] = ($bySeverity[$severity] ?? 0) + 1;
}

echo "Events by type:\n";
foreach ($byType as $type => $count) {
    echo "- {$type}: {$count}\n";
}

echo "\nEvents by severity:\n";
foreach ($bySeverity as $severity => $count) {
    echo "- {$severity}: {$count}\n";
}
```

### Driver Coaching Report

```php
$events = Motive::driverPerformanceEvents()->list([
    'driver_id' => 123,
    'start_date' => now()->subDays(30)->toDateString(),
]);

$highSeverity = [];
foreach ($events as $event) {
    if ($event->severity === EventSeverity::High) {
        $highSeverity[] = $event;
    }
}

echo "High severity events requiring coaching:\n";
foreach ($highSeverity as $event) {
    echo "- {$event->occurredAt->format('m/d')}: {$event->type->value}\n";
}
```

### Speeding Analysis

```php
$events = Motive::driverPerformanceEvents()->list([
    'event_types' => ['speeding'],
    'start_date' => now()->subDays(30)->toDateString(),
]);

$overages = [];
foreach ($events as $event) {
    if ($event->speed && $event->speedLimit) {
        $overage = $event->speed - $event->speedLimit;
        $overages[] = $overage;
    }
}

if (! empty($overages)) {
    $avgOverage = array_sum($overages) / count($overages);
    echo "Average speeding overage: " . round($avgOverage, 1) . " mph\n";
}
```

## Related

- [Scorecard](../drivers/scorecard.md)
- [Users](../drivers/users.md)

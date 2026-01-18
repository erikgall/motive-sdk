# Scorecard

The Scorecard resource provides driver safety scores based on driving behavior.

## Access

```php
use Motive\Facades\Motive;

$scorecard = Motive::scorecard();
```

## Methods

| Method | Description |
|--------|-------------|
| `forDriver($driverId, $params)` | Get scorecard for a driver |
| `forFleet($params)` | Get fleet-wide scorecard |

## Get Driver Scorecard

```php
$scorecard = Motive::scorecard()->forDriver(123, [
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Overall Score: {$scorecard->overallScore}\n";
echo "Harsh Braking: {$scorecard->harshBrakingScore}\n";
echo "Speeding: {$scorecard->speedingScore}\n";
echo "Miles Driven: {$scorecard->totalMiles}\n";
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `start_date` | string | Yes | Start date (YYYY-MM-DD) |
| `end_date` | string | Yes | End date (YYYY-MM-DD) |

## Get Fleet Scorecard

```php
$fleetScorecard = Motive::scorecard()->forFleet([
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Fleet Average Score: {$fleetScorecard->overallScore}\n";
echo "Total Miles: {$fleetScorecard->totalMiles}\n";
```

## Scorecard DTO

| Property | Type | Description |
|----------|------|-------------|
| `driverId` | int\|null | Driver ID (null for fleet) |
| `overallScore` | float | Overall safety score (0-100) |
| `harshBrakingScore` | float\|null | Harsh braking score |
| `harshAccelerationScore` | float\|null | Harsh acceleration score |
| `speedingScore` | float\|null | Speeding score |
| `corneringScore` | float\|null | Cornering score |
| `totalMiles` | float | Total miles in period |
| `totalEvents` | int\|null | Total safety events |
| `periodStart` | CarbonImmutable | Period start date |
| `periodEnd` | CarbonImmutable | Period end date |

## Use Cases

### Driver Rankings

```php
$drivers = Motive::users()->list(['role' => 'driver']);
$scores = [];

foreach ($drivers as $driver) {
    $scorecard = Motive::scorecard()->forDriver($driver->id, [
        'start_date' => now()->subDays(30)->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $scores[] = [
        'driver' => $driver,
        'score' => $scorecard->overallScore,
    ];
}

// Sort by score descending
usort($scores, fn ($a, $b) => $b['score'] <=> $a['score']);

echo "Top 5 Drivers:\n";
foreach (array_slice($scores, 0, 5) as $i => $entry) {
    echo ($i + 1) . ". {$entry['driver']->firstName}: {$entry['score']}\n";
}
```

### Safety Improvement Tracking

```php
$thisMonth = Motive::scorecard()->forDriver(123, [
    'start_date' => now()->startOfMonth()->toDateString(),
    'end_date' => now()->toDateString(),
]);

$lastMonth = Motive::scorecard()->forDriver(123, [
    'start_date' => now()->subMonth()->startOfMonth()->toDateString(),
    'end_date' => now()->subMonth()->endOfMonth()->toDateString(),
]);

$improvement = $thisMonth->overallScore - $lastMonth->overallScore;

if ($improvement > 0) {
    echo "Score improved by {$improvement} points!";
} else {
    echo "Score decreased by " . abs($improvement) . " points.";
}
```

### Fleet Safety Dashboard

```php
$fleetScore = Motive::scorecard()->forFleet([
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

return [
    'overall_score' => $fleetScore->overallScore,
    'harsh_braking' => $fleetScore->harshBrakingScore,
    'speeding' => $fleetScore->speedingScore,
    'total_miles' => $fleetScore->totalMiles,
    'events_count' => $fleetScore->totalEvents,
];
```

## Related

- [Driver Performance Events](../safety/driver-performance.md)
- [Users](users.md)

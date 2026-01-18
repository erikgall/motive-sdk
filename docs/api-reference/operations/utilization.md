# Utilization

The Utilization resource provides vehicle usage metrics and reporting.

## Access

```php
use Motive\Facades\Motive;

$utilization = Motive::utilization();
```

## Methods

| Method | Description |
|--------|-------------|
| `forVehicle($vehicleId, $params)` | Get utilization for a vehicle |
| `forFleet($params)` | Get fleet-wide utilization |
| `daily($params)` | Get daily utilization breakdown |
| `summary($params)` | Get utilization summary |

## Vehicle Utilization

```php
$report = Motive::utilization()->forVehicle(123, [
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Vehicle: {$report->vehicleId}\n";
echo "Total Miles: {$report->totalMiles}\n";
echo "Total Hours: {$report->totalHours}\n";
echo "Idle Hours: {$report->idleHours}\n";
echo "Utilization Rate: {$report->utilizationRate}%\n";
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `start_date` | string | Yes | Start date (YYYY-MM-DD) |
| `end_date` | string | Yes | End date (YYYY-MM-DD) |

## Fleet Utilization

```php
$report = Motive::utilization()->forFleet([
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Fleet Utilization\n";
echo "Total Vehicles: {$report->vehicleCount}\n";
echo "Total Miles: {$report->totalMiles}\n";
echo "Average Utilization: {$report->averageUtilizationRate}%\n";
```

## Daily Utilization

```php
$daily = Motive::utilization()->daily([
    'vehicle_id' => 123,
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($daily as $day) {
    echo "{$day->date->format('Y-m-d')}: ";
    echo "{$day->miles} miles, {$day->hours} hours\n";
}
```

## Utilization Summary

```php
$summary = Motive::utilization()->summary([
    'start_date' => now()->subMonth()->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Monthly Summary:\n";
echo "Active Vehicles: {$summary->activeVehicles}\n";
echo "Total Miles: {$summary->totalMiles}\n";
echo "Total Fuel: {$summary->totalGallons} gallons\n";
echo "Average MPG: {$summary->averageMpg}\n";
```

## UtilizationReport DTO

| Property | Type | Description |
|----------|------|-------------|
| `vehicleId` | int\|null | Vehicle ID (null for fleet) |
| `vehicleCount` | int\|null | Number of vehicles |
| `totalMiles` | float | Total miles driven |
| `totalHours` | float | Total engine hours |
| `idleHours` | float\|null | Idle time hours |
| `utilizationRate` | float\|null | Utilization percentage |
| `averageUtilizationRate` | float\|null | Fleet average |
| `totalGallons` | float\|null | Fuel consumed |
| `averageMpg` | float\|null | Average MPG |
| `periodStart` | CarbonImmutable | Report period start |
| `periodEnd` | CarbonImmutable | Report period end |

## UtilizationDay DTO

| Property | Type | Description |
|----------|------|-------------|
| `date` | CarbonImmutable | Day |
| `vehicleId` | int\|null | Vehicle ID |
| `miles` | float | Miles driven |
| `hours` | float | Engine hours |
| `idleHours` | float\|null | Idle hours |
| `gallons` | float\|null | Fuel used |

## Use Cases

### Underutilized Vehicle Report

```php
$vehicles = Motive::vehicles()->list(['status' => 'active'])->all();

$underutilized = [];
foreach ($vehicles as $vehicle) {
    $utilization = Motive::utilization()->forVehicle($vehicle->id, [
        'start_date' => now()->subDays(30)->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    if ($utilization->utilizationRate < 50) {
        $underutilized[] = [
            'vehicle' => $vehicle,
            'rate' => $utilization->utilizationRate,
        ];
    }
}

echo count($underutilized) . " vehicles under 50% utilization\n";
```

### Fuel Efficiency Analysis

```php
$report = Motive::utilization()->forFleet([
    'start_date' => now()->subMonth()->toDateString(),
    'end_date' => now()->toDateString(),
]);

$mpg = $report->totalMiles / $report->totalGallons;
echo "Fleet Average: " . round($mpg, 1) . " MPG\n";

if ($mpg < 6) {
    echo "Warning: Below expected efficiency\n";
}
```

## Related

- [Vehicles](../fleet/vehicles.md)
- [Fuel Purchases](../financial/fuel-purchases.md)

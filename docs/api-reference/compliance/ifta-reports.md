# IFTA Reports

The IFTA Reports resource generates International Fuel Tax Agreement reports for quarterly fuel tax filing.

## Access

```php
use Motive\Facades\Motive;

$iftaReports = Motive::iftaReports();
```

## Methods

| Method | Description |
|--------|-------------|
| `generate($params)` | Generate IFTA report |
| `list($params)` | List generated reports |

## Generate IFTA Report

```php
$report = Motive::iftaReports()->generate([
    'quarter' => 4,
    'year' => 2024,
]);

echo "Quarter: Q{$report->quarter} {$report->year}\n";
echo "Total Miles: {$report->totalMiles}\n";
echo "Total Gallons: {$report->totalGallons}\n";

foreach ($report->jurisdictions as $jurisdiction) {
    echo "\n{$jurisdiction->state}:\n";
    echo "  Miles: {$jurisdiction->miles}\n";
    echo "  Gallons: {$jurisdiction->gallons}\n";
    echo "  MPG: {$jurisdiction->mpg}\n";
}
```

### Generate Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `quarter` | int | Yes | Quarter (1-4) |
| `year` | int | Yes | Year |
| `vehicle_ids` | array | No | Specific vehicles |

## List Generated Reports

```php
$reports = Motive::iftaReports()->list([
    'year' => 2024,
]);

foreach ($reports as $report) {
    echo "Q{$report->quarter} {$report->year}\n";
}
```

## IftaReport DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int\|null | Report ID |
| `quarter` | int | Quarter (1-4) |
| `year` | int | Year |
| `totalMiles` | float | Total miles |
| `totalGallons` | float | Total fuel gallons |
| `averageMpg` | float\|null | Fleet average MPG |
| `jurisdictions` | array | Per-state data |
| `generatedAt` | CarbonImmutable\|null | Generation time |

## IftaJurisdiction DTO

| Property | Type | Description |
|----------|------|-------------|
| `state` | string | State/jurisdiction code |
| `miles` | float | Miles in jurisdiction |
| `gallons` | float | Gallons consumed |
| `mpg` | float | Miles per gallon |
| `taxableGallons` | float\|null | Taxable gallons |
| `taxRate` | float\|null | Tax rate |
| `taxDue` | float\|null | Tax amount due |

## Use Cases

### Quarterly Tax Filing

```php
$report = Motive::iftaReports()->generate([
    'quarter' => 4,
    'year' => 2024,
]);

$taxData = [];
foreach ($report->jurisdictions as $jurisdiction) {
    $taxData[] = [
        'state' => $jurisdiction->state,
        'miles' => $jurisdiction->miles,
        'gallons' => $jurisdiction->gallons,
        'mpg' => $jurisdiction->mpg,
        'taxable_gallons' => $jurisdiction->taxableGallons,
        'tax_due' => $jurisdiction->taxDue,
    ];
}

// Export to CSV or submit to tax system
```

### Year-End Summary

```php
$yearlyData = [];

for ($quarter = 1; $quarter <= 4; $quarter++) {
    $report = Motive::iftaReports()->generate([
        'quarter' => $quarter,
        'year' => 2024,
    ]);

    $yearlyData[$quarter] = [
        'miles' => $report->totalMiles,
        'gallons' => $report->totalGallons,
        'mpg' => $report->averageMpg,
    ];
}

$totalMiles = array_sum(array_column($yearlyData, 'miles'));
$totalGallons = array_sum(array_column($yearlyData, 'gallons'));

echo "2024 Totals:\n";
echo "Miles: {$totalMiles}\n";
echo "Gallons: {$totalGallons}\n";
echo "Average MPG: " . round($totalMiles / $totalGallons, 2) . "\n";
```

### State-by-State Analysis

```php
$report = Motive::iftaReports()->generate([
    'quarter' => 4,
    'year' => 2024,
]);

$sorted = collect($report->jurisdictions)
    ->sortByDesc('miles')
    ->values();

echo "Top 5 States by Miles:\n";
foreach ($sorted->take(5) as $i => $jurisdiction) {
    echo ($i + 1) . ". {$jurisdiction->state}: {$jurisdiction->miles} miles\n";
}
```

## Related

- [Fuel Purchases](../financial/fuel-purchases.md)
- [Vehicles](../fleet/vehicles.md)

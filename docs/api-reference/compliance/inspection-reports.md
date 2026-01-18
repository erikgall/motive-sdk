# Inspection Reports

The Inspection Reports resource manages Driver Vehicle Inspection Reports (DVIR).

## Access

```php
use Motive\Facades\Motive;

$inspectionReports = Motive::inspectionReports();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List inspection reports |
| `paginate($page, $perPage, $params)` | Get paginated reports |
| `find($id)` | Find report by ID |
| `downloadPdf($id)` | Download signed PDF |

## List Inspection Reports

```php
$reports = Motive::inspectionReports()->list([
    'start_date' => now()->subDays(7)->toDateString(),
    'vehicle_id' => 123,
]);

foreach ($reports as $report) {
    echo "Type: {$report->type}\n";
    echo "Vehicle: {$report->vehicle->number}\n";
    echo "Driver: {$report->driver->firstName}\n";
    echo "Status: {$report->status}\n";
    echo "Date: {$report->createdAt->format('Y-m-d H:i')}\n";

    if ($report->defects) {
        echo "Defects:\n";
        foreach ($report->defects as $defect) {
            echo "  - {$defect->area}: {$defect->description}\n";
        }
    }
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `vehicle_id` | int | Filter by vehicle ID |
| `driver_id` | int | Filter by driver ID |
| `status` | string | Filter by status |
| `type` | string | Filter by type |
| `per_page` | int | Items per page |

## Find a Report

```php
$report = Motive::inspectionReports()->find($reportId);

echo "Inspection Type: {$report->type}";
echo "Vehicle: {$report->vehicle->number}";
echo "Driver: {$report->driver->firstName} {$report->driver->lastName}";
```

## Download PDF

Download the signed inspection report PDF:

```php
$pdf = Motive::inspectionReports()->downloadPdf($reportId);

// Save to file
file_put_contents('inspection-report.pdf', $pdf);

// Or return as download response in Laravel
return response($pdf)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="inspection.pdf"');
```

## InspectionReport DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Report ID |
| `type` | InspectionType | Pre-trip or post-trip |
| `status` | InspectionStatus | Report status |
| `vehicleId` | int | Vehicle ID |
| `vehicle` | Vehicle\|null | Vehicle details |
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `defects` | array\|null | List of defects |
| `notes` | string\|null | Additional notes |
| `signedAt` | CarbonImmutable\|null | Signature time |
| `createdAt` | CarbonImmutable | Created timestamp |

## InspectionDefect DTO

| Property | Type | Description |
|----------|------|-------------|
| `area` | string | Vehicle area |
| `description` | string | Defect description |
| `severity` | string\|null | Defect severity |
| `repaired` | bool | Repair status |
| `repairedAt` | CarbonImmutable\|null | Repair time |

## InspectionType Enum

| Value | Description |
|-------|-------------|
| `pre_trip` | Pre-trip inspection |
| `post_trip` | Post-trip inspection |

## InspectionStatus Enum

| Value | Description |
|-------|-------------|
| `satisfactory` | No defects found |
| `defects_found` | Defects reported |
| `defects_corrected` | Defects have been repaired |

## Use Cases

### DVIR Compliance Dashboard

```php
$reports = Motive::inspectionReports()->list([
    'start_date' => now()->subDays(7)->toDateString(),
]);

$stats = [
    'total' => 0,
    'satisfactory' => 0,
    'with_defects' => 0,
    'pre_trip' => 0,
    'post_trip' => 0,
];

foreach ($reports as $report) {
    $stats['total']++;
    $stats[$report->status === 'satisfactory' ? 'satisfactory' : 'with_defects']++;
    $stats[$report->type]++;
}

echo "Total inspections: {$stats['total']}\n";
echo "Satisfactory: {$stats['satisfactory']}\n";
echo "With defects: {$stats['with_defects']}\n";
```

### Outstanding Defect Report

```php
$reports = Motive::inspectionReports()->list([
    'status' => 'defects_found',
    'start_date' => now()->subDays(30)->toDateString(),
]);

$outstanding = [];
foreach ($reports as $report) {
    foreach ($report->defects as $defect) {
        if (! $defect->repaired) {
            $outstanding[] = [
                'vehicle' => $report->vehicle->number,
                'area' => $defect->area,
                'description' => $defect->description,
                'reported' => $report->createdAt,
            ];
        }
    }
}

echo count($outstanding) . " outstanding defects\n";
```

## Related

- [Vehicles](../fleet/vehicles.md)
- [InspectionType Enum](../../enums/type-enums.md)
- [InspectionStatus Enum](../../enums/status-enums.md)

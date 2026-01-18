# Timecards

The Timecards resource manages driver time tracking records.

## Access

```php
use Motive\Facades\Motive;

$timecards = Motive::timecards();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List timecards |
| `paginate($page, $perPage, $params)` | Get paginated timecards |
| `find($id)` | Find timecard by ID |
| `update($id, $data)` | Update a timecard |

## List Timecards

```php
$timecards = Motive::timecards()->list([
    'driver_id' => 123,
    'start_date' => now()->startOfWeek()->toDateString(),
    'end_date' => now()->endOfWeek()->toDateString(),
]);

foreach ($timecards as $timecard) {
    echo "Driver: {$timecard->driverId}\n";
    echo "Date: {$timecard->date}\n";
    echo "Status: {$timecard->status->value}\n";
    echo "Total Hours: " . ($timecard->totalMinutes / 60) . "\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_id` | int | Filter by driver |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `status` | string | Filter by status |
| `per_page` | int | Items per page |

## Find a Timecard

```php
$timecard = Motive::timecards()->find($timecardId);

echo "Date: {$timecard->date}";
echo "Status: {$timecard->status->value}";

foreach ($timecard->entries as $entry) {
    echo "  {$entry->type}: {$entry->startTime} - {$entry->endTime}\n";
}
```

## Update a Timecard

```php
use Motive\Enums\TimecardStatus;

$timecard = Motive::timecards()->update($timecardId, [
    'status' => TimecardStatus::Approved->value,
    'notes' => 'Approved by manager',
]);
```

## Timecard DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Timecard ID |
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `date` | CarbonImmutable | Timecard date |
| `status` | TimecardStatus | Approval status |
| `totalMinutes` | int | Total time in minutes |
| `entries` | array\|null | Time entries |
| `notes` | string\|null | Notes |
| `approvedAt` | CarbonImmutable\|null | Approval time |
| `approvedBy` | int\|null | Approver user ID |

## TimecardEntry DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Entry ID |
| `type` | string | Entry type |
| `startTime` | CarbonImmutable | Start time |
| `endTime` | CarbonImmutable\|null | End time |
| `duration` | int\|null | Duration in minutes |

## TimecardStatus Enum

| Value | Description |
|-------|-------------|
| `pending` | Awaiting approval |
| `approved` | Approved |
| `rejected` | Rejected |

## Use Cases

### Weekly Payroll Report

```php
$timecards = Motive::timecards()->list([
    'start_date' => now()->startOfWeek()->toDateString(),
    'end_date' => now()->endOfWeek()->toDateString(),
    'status' => 'approved',
]);

$byDriver = collect($timecards)->groupBy('driverId');

foreach ($byDriver as $driverId => $driverTimecards) {
    $totalHours = collect($driverTimecards)->sum('totalMinutes') / 60;
    echo "Driver {$driverId}: {$totalHours} hours\n";
}
```

### Pending Approvals

```php
$pending = Motive::timecards()->list([
    'status' => 'pending',
    'start_date' => now()->subDays(14)->toDateString(),
]);

echo count($pending) . " timecards pending approval\n";

foreach ($pending as $timecard) {
    echo "Driver {$timecard->driverId}, {$timecard->date}: ";
    echo round($timecard->totalMinutes / 60, 1) . " hours\n";
}
```

### Bulk Approval

```php
$pending = Motive::timecards()->list([
    'status' => 'pending',
    'start_date' => now()->subWeek()->toDateString(),
]);

foreach ($pending as $timecard) {
    Motive::timecards()->update($timecard->id, [
        'status' => TimecardStatus::Approved->value,
    ]);
}

echo count($pending) . " timecards approved";
```

## Related

- [Users](../drivers/users.md)
- [HOS Logs](../compliance/hos-logs.md)

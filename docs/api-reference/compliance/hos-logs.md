# HOS Logs

The HOS Logs resource manages Hours of Service records for regulatory compliance.

## Access

```php
use Motive\Facades\Motive;

$hosLogs = Motive::hosLogs();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List HOS logs |
| `paginate($page, $perPage, $params)` | Get paginated logs |
| `find($id)` | Find log by ID |
| `create($data)` | Create a log entry |
| `update($id, $data)` | Update a log entry |
| `delete($id)` | Delete a log entry |
| `certify($driverId, $date)` | Certify logs for a date |

## List HOS Logs

```php
use Motive\Enums\DutyStatus;

$logs = Motive::hosLogs()->list([
    'driver_ids' => [123, 456],
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($logs as $log) {
    echo "{$log->driver->firstName} {$log->driver->lastName}\n";
    echo "Status: {$log->status->value}\n";
    echo "Start: {$log->startTime->format('Y-m-d H:i:s')}\n";
    echo "Duration: {$log->duration} minutes\n";
    echo "Location: {$log->location}\n";
}

// Filter by duty status
$drivingLogs = Motive::hosLogs()->list([
    'driver_ids' => [123],
    'duty_status' => DutyStatus::Driving->value,
]);
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_ids` | array | Filter by driver IDs |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `duty_status` | string | Filter by status |
| `per_page` | int | Items per page |

## Find a Log Entry

```php
$log = Motive::hosLogs()->find($logId);

echo $log->driver->firstName;
echo $log->status->value;
echo $log->startTime;
```

## Create a Log Entry

```php
use Motive\Enums\DutyStatus;

$log = Motive::hosLogs()->create([
    'driver_id' => 123,
    'status' => DutyStatus::OnDuty->value,
    'start_time' => now()->toIso8601String(),
    'location' => 'Dallas, TX',
    'notes' => 'Pre-trip inspection',
]);
```

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `driver_id` | int | Yes | Driver ID |
| `status` | string | Yes | Duty status |
| `start_time` | string | Yes | Start time (ISO 8601) |
| `location` | string | No | Location description |
| `notes` | string | No | Log notes |
| `vehicle_id` | int | No | Vehicle ID |

## Update a Log Entry

```php
$log = Motive::hosLogs()->update($logId, [
    'status' => DutyStatus::Driving->value,
    'annotation' => 'Corrected status from On Duty to Driving',
]);
```

## Delete a Log Entry

```php
$deleted = Motive::hosLogs()->delete($logId);
```

## Certify Logs

Certify a driver's logs for a specific date:

```php
Motive::hosLogs()->certify(
    driverId: 123,
    date: now()->subDay()->toDateString()
);
```

## HosLog DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Log ID |
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `vehicleId` | int\|null | Vehicle ID |
| `status` | DutyStatus | Duty status |
| `startTime` | CarbonImmutable | Start time |
| `endTime` | CarbonImmutable\|null | End time |
| `duration` | int\|null | Duration in minutes |
| `location` | string\|null | Location |
| `notes` | string\|null | Notes |
| `annotation` | string\|null | Edit annotation |
| `certified` | bool | Certification status |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## DutyStatus Enum

| Value | Description |
|-------|-------------|
| `off_duty` | Off Duty |
| `sleeper_berth` | Sleeper Berth |
| `driving` | Driving |
| `on_duty` | On Duty (Not Driving) |
| `yard_move` | Yard Move |
| `personal_conveyance` | Personal Conveyance |

## Use Cases

### Daily Log Summary

```php
$logs = Motive::hosLogs()->list([
    'driver_ids' => [123],
    'start_date' => now()->toDateString(),
    'end_date' => now()->toDateString(),
]);

$summary = [];
foreach ($logs as $log) {
    $status = $log->status->value;
    $summary[$status] = ($summary[$status] ?? 0) + ($log->duration ?? 0);
}

foreach ($summary as $status => $minutes) {
    echo "{$status}: " . round($minutes / 60, 1) . " hours\n";
}
```

### Compliance Report

```php
$logs = Motive::hosLogs()->list([
    'start_date' => now()->subDays(7)->toDateString(),
    'end_date' => now()->toDateString(),
]);

$uncertified = [];
foreach ($logs as $log) {
    if (! $log->certified) {
        $uncertified[] = $log;
    }
}

echo count($uncertified) . " uncertified logs found";
```

## Related

- [HOS Availability](hos-availability.md)
- [HOS Violations](hos-violations.md)
- [DutyStatus Enum](../../enums/hos-enums.md)

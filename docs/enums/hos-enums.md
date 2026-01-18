# HOS Enums

## DutyStatus

Driver duty status for Hours of Service.

```php
use Motive\Enums\DutyStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `OffDuty` | `off_duty` | Off Duty |
| `SleeperBerth` | `sleeper_berth` | Sleeper Berth |
| `Driving` | `driving` | Driving |
| `OnDuty` | `on_duty` | On Duty (Not Driving) |
| `YardMove` | `yard_move` | Yard Move |
| `PersonalConveyance` | `personal_conveyance` | Personal Conveyance |

### Usage

```php
use Motive\Enums\DutyStatus;

// Create a log entry
$log = Motive::hosLogs()->create([
    'driver_id' => 123,
    'status' => DutyStatus::OnDuty->value,
    'start_time' => now()->toIso8601String(),
    'location' => 'Dallas, TX',
]);

// Filter logs by status
$drivingLogs = Motive::hosLogs()->list([
    'driver_ids' => [123],
    'duty_status' => DutyStatus::Driving->value,
]);

// Check current status
$availability = Motive::hosAvailability()->forDriver(123);

if ($availability->currentStatus === DutyStatus::Driving) {
    echo "Driver is currently driving";
}
```

### Status Descriptions

| Status | Description | Counts Against |
|--------|-------------|----------------|
| Off Duty | Not working, personal time | Nothing |
| Sleeper Berth | Resting in sleeper berth | Nothing |
| Driving | Operating the vehicle | Drive time, shift time, cycle time |
| On Duty | Working but not driving | Shift time, cycle time |
| Yard Move | Moving vehicle in yard | Shift time (not drive time) |
| Personal Conveyance | Personal use of vehicle | Nothing |

---

## HosViolationType

Types of HOS rule violations.

```php
use Motive\Enums\HosViolationType;
```

| Case | Value | Description |
|------|-------|-------------|
| `ElevenHour` | `11_hour` | Exceeded 11-hour driving limit |
| `FourteenHour` | `14_hour` | Exceeded 14-hour shift limit |
| `ThirtyMinuteBreak` | `30_minute_break` | Missing required break |
| `SeventyHour` | `70_hour` | Exceeded 70-hour cycle limit |
| `RestBreak` | `rest_break` | Insufficient rest period |

### Usage

```php
use Motive\Enums\HosViolationType;

$violations = Motive::hosViolations()->list([
    'driver_ids' => [123],
    'violation_type' => HosViolationType::ElevenHour->value,
]);

// Analyze by type
$byType = [];
foreach ($violations as $violation) {
    $type = $violation->type->value;
    $byType[$type] = ($byType[$type] ?? 0) + 1;
}
```

### Violation Rules

| Violation | Rule |
|-----------|------|
| 11-Hour | Max 11 hours driving in 14-hour window |
| 14-Hour | Max 14-hour on-duty window after 10 hours off |
| 30-Minute Break | 30-minute break required before 8 hours driving |
| 70-Hour | Max 70 hours in 8 consecutive days |
| Rest Break | 10 consecutive hours off required between shifts |

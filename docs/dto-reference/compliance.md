# Compliance DTOs

## HosLog

Represents a Hours of Service log entry.

```php
use Motive\Data\HosLog;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Log ID |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `vehicleId` | `int\|null` | Vehicle ID |
| `status` | `DutyStatus` | Duty status |
| `startTime` | `CarbonImmutable` | Start time |
| `endTime` | `CarbonImmutable\|null` | End time |
| `duration` | `int\|null` | Duration in minutes |
| `location` | `string\|null` | Location |
| `notes` | `string\|null` | Notes |
| `annotation` | `string\|null` | Edit annotation |
| `certified` | `bool` | Certification status |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## HosAvailability

Represents a driver's remaining Hours of Service.

```php
use Motive\Data\HosAvailability;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `driveTimeRemaining` | `int` | Remaining drive minutes |
| `shiftTimeRemaining` | `int` | Remaining shift minutes |
| `cycleTimeRemaining` | `int` | Remaining cycle minutes |
| `breakTimeRequired` | `int\|null` | Minutes until break required |
| `currentStatus` | `DutyStatus\|null` | Current duty status |
| `lastUpdated` | `CarbonImmutable\|null` | Last update time |

---

## HosViolation

Represents a Hours of Service violation.

```php
use Motive\Data\HosViolation;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Violation ID |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `type` | `HosViolationType` | Violation type |
| `startTime` | `CarbonImmutable` | Violation start |
| `endTime` | `CarbonImmutable\|null` | Violation end |
| `duration` | `int\|null` | Duration in minutes |
| `description` | `string\|null` | Description |

---

## InspectionReport

Represents a DVIR inspection report.

```php
use Motive\Data\InspectionReport;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Report ID |
| `type` | `InspectionType` | Pre-trip or post-trip |
| `status` | `InspectionStatus` | Report status |
| `vehicleId` | `int` | Vehicle ID |
| `vehicle` | `Vehicle\|null` | Vehicle details |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `defects` | `array\|null` | List of defects |
| `notes` | `string\|null` | Notes |
| `signedAt` | `CarbonImmutable\|null` | Signature time |
| `createdAt` | `CarbonImmutable` | Created timestamp |

---

## InspectionDefect

Represents a defect reported in an inspection.

```php
use Motive\Data\InspectionDefect;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `area` | `string` | Vehicle area |
| `description` | `string` | Defect description |
| `severity` | `string\|null` | Severity level |
| `repaired` | `bool` | Repair status |
| `repairedAt` | `CarbonImmutable\|null` | Repair time |
| `repairedBy` | `int\|null` | Repairing user ID |

---

## IftaReport

Represents an IFTA fuel tax report.

```php
use Motive\Data\IftaReport;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int\|null` | Report ID |
| `quarter` | `int` | Quarter (1-4) |
| `year` | `int` | Year |
| `totalMiles` | `float` | Total miles |
| `totalGallons` | `float` | Total gallons |
| `averageMpg` | `float\|null` | Average MPG |
| `jurisdictions` | `array` | Per-state data |
| `generatedAt` | `CarbonImmutable\|null` | Generation time |

---

## IftaJurisdiction

Represents IFTA data for a single jurisdiction.

```php
use Motive\Data\IftaJurisdiction;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `state` | `string` | State code |
| `miles` | `float` | Miles in jurisdiction |
| `gallons` | `float` | Gallons consumed |
| `mpg` | `float` | Miles per gallon |
| `taxableGallons` | `float\|null` | Taxable gallons |
| `taxRate` | `float\|null` | Tax rate |
| `taxDue` | `float\|null` | Tax amount due |

## Related

- [HOS Logs Resource](../api-reference/compliance/hos-logs.md)
- [DutyStatus Enum](../enums/hos-enums.md)
- [HosViolationType Enum](../enums/hos-enums.md)

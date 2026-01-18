# Operations DTOs

## Group

Represents an organizational group.

```php
use Motive\Data\Group;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Group ID |
| `name` | `string` | Group name |
| `description` | `string\|null` | Description |
| `members` | `array\|null` | Group members |
| `externalId` | `string\|null` | External ID |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## GroupMember

Represents a group membership.

```php
use Motive\Data\GroupMember;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `memberId` | `int` | Member ID |
| `type` | `string` | Member type |
| `addedAt` | `CarbonImmutable\|null` | When added |

---

## Company

Represents the Motive company account.

```php
use Motive\Data\Company;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Company ID |
| `name` | `string` | Company name |
| `dotNumber` | `string\|null` | DOT number |
| `mcNumber` | `string\|null` | MC number |
| `address` | `string\|null` | Address |
| `city` | `string\|null` | City |
| `state` | `string\|null` | State |
| `postalCode` | `string\|null` | Postal code |
| `country` | `string\|null` | Country |
| `phone` | `string\|null` | Phone |
| `email` | `string\|null` | Email |
| `timezone` | `string\|null` | Timezone |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## Form

Represents a custom form template.

```php
use Motive\Data\Form;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Form ID |
| `name` | `string` | Form name |
| `description` | `string\|null` | Description |
| `fields` | `array\|null` | Form fields |
| `active` | `bool` | Active status |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## FormField

Represents a form field definition.

```php
use Motive\Data\FormField;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Field ID |
| `name` | `string` | Field name |
| `type` | `FormFieldType` | Field type |
| `required` | `bool` | Required status |
| `options` | `array\|null` | Select options |
| `order` | `int\|null` | Display order |

---

## FormEntry

Represents a submitted form.

```php
use Motive\Data\FormEntry;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Entry ID |
| `formId` | `int` | Form template ID |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `values` | `array` | Field values |
| `submittedAt` | `CarbonImmutable` | Submission time |
| `latitude` | `float\|null` | Location |
| `longitude` | `float\|null` | Location |

---

## Timecard

Represents a time tracking record.

```php
use Motive\Data\Timecard;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Timecard ID |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `date` | `CarbonImmutable` | Date |
| `status` | `TimecardStatus` | Approval status |
| `totalMinutes` | `int` | Total minutes |
| `entries` | `array\|null` | Time entries |
| `notes` | `string\|null` | Notes |
| `approvedAt` | `CarbonImmutable\|null` | Approval time |
| `approvedBy` | `int\|null` | Approver ID |

---

## TimecardEntry

Represents a time entry.

```php
use Motive\Data\TimecardEntry;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Entry ID |
| `type` | `string` | Entry type |
| `startTime` | `CarbonImmutable` | Start time |
| `endTime` | `CarbonImmutable\|null` | End time |
| `duration` | `int\|null` | Minutes |

---

## UtilizationReport

Represents utilization metrics.

```php
use Motive\Data\UtilizationReport;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `vehicleId` | `int\|null` | Vehicle ID |
| `vehicleCount` | `int\|null` | Vehicle count |
| `totalMiles` | `float` | Total miles |
| `totalHours` | `float` | Total hours |
| `idleHours` | `float\|null` | Idle hours |
| `utilizationRate` | `float\|null` | Utilization % |
| `totalGallons` | `float\|null` | Fuel consumed |
| `averageMpg` | `float\|null` | Average MPG |
| `periodStart` | `CarbonImmutable` | Period start |
| `periodEnd` | `CarbonImmutable` | Period end |

---

## UtilizationDay

Represents daily utilization.

```php
use Motive\Data\UtilizationDay;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `date` | `CarbonImmutable` | Day |
| `vehicleId` | `int\|null` | Vehicle ID |
| `miles` | `float` | Miles driven |
| `hours` | `float` | Engine hours |
| `idleHours` | `float\|null` | Idle hours |
| `gallons` | `float\|null` | Fuel used |

## Related

- [Groups Resource](../api-reference/operations/groups.md)
- [Timecards Resource](../api-reference/operations/timecards.md)
- [TimecardStatus Enum](../enums/status-enums.md)

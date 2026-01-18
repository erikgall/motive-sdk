# Status Enums

## VehicleStatus

Vehicle operational status.

```php
use Motive\Enums\VehicleStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Active` | `active` | Vehicle is active |
| `Inactive` | `inactive` | Vehicle is inactive |

### Usage

```php
$vehicles = Motive::vehicles()->list([
    'status' => VehicleStatus::Active->value,
]);

if ($vehicle->status === VehicleStatus::Active) {
    // Vehicle is active
}
```

---

## AssetStatus

Asset operational status.

```php
use Motive\Enums\AssetStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Active` | `active` | Asset is active |
| `Inactive` | `inactive` | Asset is inactive |

---

## UserStatus

User account status.

```php
use Motive\Enums\UserStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Active` | `active` | User is active |
| `Inactive` | `inactive` | User is deactivated |
| `Pending` | `pending` | Awaiting activation |

---

## DispatchStatus

Dispatch lifecycle status.

```php
use Motive\Enums\DispatchStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Pending` | `pending` | Not yet started |
| `InProgress` | `in_progress` | Currently active |
| `Completed` | `completed` | Successfully completed |
| `Cancelled` | `cancelled` | Cancelled |

### Usage

```php
$active = Motive::dispatches()->list([
    'status' => DispatchStatus::InProgress->value,
]);
```

---

## DocumentStatus

Document review status.

```php
use Motive\Enums\DocumentStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Pending` | `pending` | Awaiting review |
| `Approved` | `approved` | Approved |
| `Rejected` | `rejected` | Rejected |

---

## InspectionStatus

DVIR inspection result status.

```php
use Motive\Enums\InspectionStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Satisfactory` | `satisfactory` | No defects found |
| `DefectsFound` | `defects_found` | Defects reported |
| `DefectsCorrected` | `defects_corrected` | Defects repaired |

---

## WebhookStatus

Webhook subscription status.

```php
use Motive\Enums\WebhookStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Active` | `active` | Receiving events |
| `Inactive` | `inactive` | Disabled |
| `Failing` | `failing` | Multiple failures |

---

## TimecardStatus

Timecard approval status.

```php
use Motive\Enums\TimecardStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Pending` | `pending` | Awaiting approval |
| `Approved` | `approved` | Approved |
| `Rejected` | `rejected` | Rejected |

---

## VideoStatus

Video request status.

```php
use Motive\Enums\VideoStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Pending` | `pending` | Request submitted |
| `Processing` | `processing` | Being retrieved |
| `Ready` | `ready` | Ready for download |
| `Failed` | `failed` | Request failed |
| `Expired` | `expired` | Download URL expired |

---

## ShipmentStatus

Freight shipment status.

```php
use Motive\Enums\ShipmentStatus;
```

| Case | Value | Description |
|------|-------|-------------|
| `Pending` | `pending` | Not yet started |
| `InTransit` | `in_transit` | In transit |
| `Delivered` | `delivered` | Delivered |
| `Cancelled` | `cancelled` | Cancelled |

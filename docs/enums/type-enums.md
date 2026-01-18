# Type Enums

## AssetType

Asset classification.

```php
use Motive\Enums\AssetType;
```

| Case | Value | Description |
|------|-------|-------------|
| `Trailer` | `trailer` | Trailer |
| `Container` | `container` | Container |
| `Chassis` | `chassis` | Chassis |
| `Other` | `other` | Other equipment |

---

## GeofenceType

Geofence shape type.

```php
use Motive\Enums\GeofenceType;
```

| Case | Value | Description |
|------|-------|-------------|
| `Circle` | `circle` | Circular geofence |
| `Polygon` | `polygon` | Polygon geofence |

### Usage

```php
// Create circular geofence
$geofence = Motive::geofences()->create([
    'name' => 'Customer Site',
    'type' => GeofenceType::Circle->value,
    'latitude' => 32.7767,
    'longitude' => -96.7970,
    'radius' => 500,
]);
```

---

## StopType

Dispatch stop type.

```php
use Motive\Enums\StopType;
```

| Case | Value | Description |
|------|-------|-------------|
| `Pickup` | `pickup` | Pickup location |
| `Delivery` | `delivery` | Delivery location |
| `Stop` | `stop` | Intermediate stop |

---

## DocumentType

Document classification.

```php
use Motive\Enums\DocumentType;
```

| Case | Value | Description |
|------|-------|-------------|
| `BillOfLading` | `bill_of_lading` | Bill of Lading |
| `ProofOfDelivery` | `proof_of_delivery` | Proof of Delivery |
| `Receipt` | `receipt` | Receipt |
| `Inspection` | `inspection` | Inspection document |
| `Other` | `other` | Other document |

---

## InspectionType

DVIR inspection type.

```php
use Motive\Enums\InspectionType;
```

| Case | Value | Description |
|------|-------|-------------|
| `PreTrip` | `pre_trip` | Pre-trip inspection |
| `PostTrip` | `post_trip` | Post-trip inspection |

---

## PerformanceEventType

Safety event classification.

```php
use Motive\Enums\PerformanceEventType;
```

| Case | Value | Description |
|------|-------|-------------|
| `HarshBraking` | `harsh_braking` | Hard braking |
| `RapidAcceleration` | `rapid_acceleration` | Rapid acceleration |
| `Speeding` | `speeding` | Exceeding speed limit |
| `Cornering` | `cornering` | Hard cornering |
| `Seatbelt` | `seatbelt` | Seatbelt unbuckled |
| `Distraction` | `distraction` | Driver distraction |
| `Fatigue` | `fatigue` | Drowsiness detected |

### Usage

```php
$events = Motive::driverPerformanceEvents()->list([
    'event_types' => [
        PerformanceEventType::HarshBraking->value,
        PerformanceEventType::Speeding->value,
    ],
]);
```

---

## FormFieldType

Form field input type.

```php
use Motive\Enums\FormFieldType;
```

| Case | Value | Description |
|------|-------|-------------|
| `Text` | `text` | Text input |
| `Number` | `number` | Numeric input |
| `Date` | `date` | Date picker |
| `Time` | `time` | Time picker |
| `Select` | `select` | Dropdown |
| `Checkbox` | `checkbox` | Checkbox |
| `Signature` | `signature` | Signature capture |
| `Photo` | `photo` | Photo attachment |

---

## CardTransactionType

Fuel card transaction type.

```php
use Motive\Enums\CardTransactionType;
```

| Case | Value | Description |
|------|-------|-------------|
| `Fuel` | `fuel` | Fuel purchase |
| `Maintenance` | `maintenance` | Vehicle maintenance |
| `Other` | `other` | Other purchase |

---

## CameraType

Dashboard camera type.

```php
use Motive\Enums\CameraType;
```

| Case | Value | Description |
|------|-------|-------------|
| `RoadFacing` | `road_facing` | Forward-facing |
| `DriverFacing` | `driver_facing` | Inward-facing |
| `Dual` | `dual` | Both cameras |

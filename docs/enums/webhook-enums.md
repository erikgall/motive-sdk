# Webhook & Other Enums

## WebhookEvent

Available webhook event types.

```php
use Motive\Enums\WebhookEvent;
```

| Case | Value | Description |
|------|-------|-------------|
| `VehicleLocationUpdated` | `vehicle.location.updated` | Vehicle position changed |
| `VehicleCreated` | `vehicle.created` | New vehicle added |
| `VehicleUpdated` | `vehicle.updated` | Vehicle data updated |
| `VehicleDeleted` | `vehicle.deleted` | Vehicle removed |
| `DriverStatusChanged` | `driver.status.changed` | Driver duty status changed |
| `HosViolationDetected` | `hos.violation.detected` | HOS violation occurred |
| `DispatchCreated` | `dispatch.created` | New dispatch created |
| `DispatchStatusChanged` | `dispatch.status.changed` | Dispatch status updated |
| `GeofenceEntered` | `geofence.entered` | Vehicle entered geofence |
| `GeofenceExited` | `geofence.exited` | Vehicle exited geofence |
| `SafetyEventDetected` | `safety.event.detected` | Safety event occurred |
| `InspectionCompleted` | `inspection.completed` | DVIR completed |
| `DocumentUploaded` | `document.uploaded` | Document uploaded |

### Usage

```php
use Motive\Enums\WebhookEvent;

$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::HosViolationDetected->value,
        WebhookEvent::DispatchStatusChanged->value,
        WebhookEvent::GeofenceEntered->value,
        WebhookEvent::GeofenceExited->value,
    ],
]);
```

---

## MessageDirection

Message direction for driver communication.

```php
use Motive\Enums\MessageDirection;
```

| Case | Value | Description |
|------|-------|-------------|
| `Inbound` | `inbound` | From driver to dispatch |
| `Outbound` | `outbound` | From dispatch to driver |

### Usage

```php
$messages = Motive::messages()->list([
    'driver_id' => 123,
    'direction' => MessageDirection::Outbound->value,
]);
```

---

## EventSeverity

Safety event severity levels.

```php
use Motive\Enums\EventSeverity;
```

| Case | Value | Description |
|------|-------|-------------|
| `Low` | `low` | Minor event |
| `Medium` | `medium` | Moderate event |
| `High` | `high` | Severe event |

### Usage

```php
// Get high severity events
$events = Motive::driverPerformanceEvents()->list([
    'severity' => EventSeverity::High->value,
]);

// Filter in code
foreach ($events as $event) {
    if ($event->severity === EventSeverity::High) {
        // Send alert
    }
}
```

---

## UserRole

User role types.

```php
use Motive\Enums\UserRole;
```

| Case | Value | Description |
|------|-------|-------------|
| `Driver` | `driver` | Driver role |
| `Admin` | `admin` | Administrator |
| `Dispatcher` | `dispatcher` | Dispatcher |
| `SafetyManager` | `safety_manager` | Safety manager |
| `FleetManager` | `fleet_manager` | Fleet manager |

### Usage

```php
// Get all drivers
$drivers = Motive::users()->list([
    'role' => UserRole::Driver->value,
]);

// Get admins
$admins = Motive::users()->list([
    'role' => UserRole::Admin->value,
]);
```

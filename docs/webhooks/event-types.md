# Webhook Event Types

Complete reference for all available webhook events.

## Event Enum

```php
use Motive\Enums\WebhookEvent;
```

## Vehicle Events

### `vehicle.location.updated`

Fired when a vehicle's GPS position is updated.

```php
WebhookEvent::VehicleLocationUpdated->value
```

**Payload Data:**

```json
{
    "vehicle_id": 123,
    "latitude": 32.7767,
    "longitude": -96.7970,
    "heading": 180,
    "speed": 65,
    "odometer": 125432.5,
    "engine_hours": 4521.3
}
```

### `vehicle.created`

Fired when a new vehicle is added.

```php
WebhookEvent::VehicleCreated->value
```

**Payload Data:**

```json
{
    "vehicle_id": 123,
    "number": "TRUCK-001",
    "vin": "1HGBH41JXMN109186",
    "make": "Freightliner",
    "model": "Cascadia",
    "year": 2023
}
```

### `vehicle.updated`

Fired when vehicle data is modified.

```php
WebhookEvent::VehicleUpdated->value
```

**Payload Data:**

```json
{
    "vehicle_id": 123,
    "changes": {
        "number": {"old": "TRUCK-001", "new": "TRUCK-001A"},
        "status": {"old": "active", "new": "inactive"}
    }
}
```

### `vehicle.deleted`

Fired when a vehicle is removed.

```php
WebhookEvent::VehicleDeleted->value
```

**Payload Data:**

```json
{
    "vehicle_id": 123,
    "number": "TRUCK-001"
}
```

## Driver Events

### `driver.status.changed`

Fired when a driver's duty status changes.

```php
WebhookEvent::DriverStatusChanged->value
```

**Payload Data:**

```json
{
    "driver_id": 456,
    "previous_status": "off_duty",
    "status": "driving",
    "changed_at": "2024-01-15T08:00:00Z",
    "vehicle_id": 123,
    "location": {
        "latitude": 32.7767,
        "longitude": -96.7970
    }
}
```

## HOS Events

### `hos.violation.detected`

Fired when an Hours of Service violation is detected.

```php
WebhookEvent::HosViolationDetected->value
```

**Payload Data:**

```json
{
    "violation_id": 789,
    "driver_id": 456,
    "violation_type": "11_hour",
    "detected_at": "2024-01-15T22:00:00Z",
    "details": {
        "hours_driven": 11.5,
        "limit": 11
    }
}
```

## Dispatch Events

### `dispatch.created`

Fired when a new dispatch is created.

```php
WebhookEvent::DispatchCreated->value
```

**Payload Data:**

```json
{
    "dispatch_id": 321,
    "driver_id": 456,
    "vehicle_id": 123,
    "status": "pending",
    "stops": [
        {
            "type": "pickup",
            "location_id": 100,
            "scheduled_arrival": "2024-01-16T08:00:00Z"
        },
        {
            "type": "delivery",
            "location_id": 101,
            "scheduled_arrival": "2024-01-16T14:00:00Z"
        }
    ]
}
```

### `dispatch.status.changed`

Fired when a dispatch status is updated.

```php
WebhookEvent::DispatchStatusChanged->value
```

**Payload Data:**

```json
{
    "dispatch_id": 321,
    "previous_status": "pending",
    "status": "in_progress",
    "changed_at": "2024-01-16T07:30:00Z",
    "driver_id": 456
}
```

## Geofence Events

### `geofence.entered`

Fired when a vehicle enters a geofence.

```php
WebhookEvent::GeofenceEntered->value
```

**Payload Data:**

```json
{
    "geofence_id": 50,
    "geofence_name": "Customer Site A",
    "vehicle_id": 123,
    "driver_id": 456,
    "entered_at": "2024-01-16T09:15:00Z",
    "location": {
        "latitude": 32.7767,
        "longitude": -96.7970
    }
}
```

### `geofence.exited`

Fired when a vehicle exits a geofence.

```php
WebhookEvent::GeofenceExited->value
```

**Payload Data:**

```json
{
    "geofence_id": 50,
    "geofence_name": "Customer Site A",
    "vehicle_id": 123,
    "driver_id": 456,
    "exited_at": "2024-01-16T10:45:00Z",
    "dwell_time_minutes": 90
}
```

## Safety Events

### `safety.event.detected`

Fired when a safety event is detected.

```php
WebhookEvent::SafetyEventDetected->value
```

**Payload Data:**

```json
{
    "event_id": 999,
    "driver_id": 456,
    "vehicle_id": 123,
    "event_type": "harsh_braking",
    "severity": "high",
    "detected_at": "2024-01-16T11:30:00Z",
    "location": {
        "latitude": 32.7767,
        "longitude": -96.7970
    },
    "speed_at_event": 55,
    "g_force": 0.85
}
```

## Inspection Events

### `inspection.completed`

Fired when a DVIR inspection is completed.

```php
WebhookEvent::InspectionCompleted->value
```

**Payload Data:**

```json
{
    "inspection_id": 1234,
    "driver_id": 456,
    "vehicle_id": 123,
    "type": "pre_trip",
    "status": "defects_found",
    "completed_at": "2024-01-16T06:00:00Z",
    "defects": [
        {
            "category": "tires",
            "description": "Low pressure on front left tire"
        }
    ]
}
```

## Document Events

### `document.uploaded`

Fired when a document is uploaded.

```php
WebhookEvent::DocumentUploaded->value
```

**Payload Data:**

```json
{
    "document_id": 5678,
    "driver_id": 456,
    "dispatch_id": 321,
    "document_type": "proof_of_delivery",
    "uploaded_at": "2024-01-16T14:30:00Z",
    "file_name": "pod_321.pdf"
}
```

## Subscribing to Events

### Single Event

```php
$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/location',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
    ],
]);
```

### Multiple Events

```php
$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/fleet',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::VehicleCreated->value,
        WebhookEvent::VehicleUpdated->value,
        WebhookEvent::VehicleDeleted->value,
        WebhookEvent::GeofenceEntered->value,
        WebhookEvent::GeofenceExited->value,
    ],
]);
```

### All Events (By Category)

```php
// Compliance monitoring
$complianceEvents = [
    WebhookEvent::DriverStatusChanged->value,
    WebhookEvent::HosViolationDetected->value,
    WebhookEvent::InspectionCompleted->value,
];

// Safety monitoring
$safetyEvents = [
    WebhookEvent::SafetyEventDetected->value,
    WebhookEvent::HosViolationDetected->value,
];

// Dispatch tracking
$dispatchEvents = [
    WebhookEvent::DispatchCreated->value,
    WebhookEvent::DispatchStatusChanged->value,
    WebhookEvent::GeofenceEntered->value,
    WebhookEvent::GeofenceExited->value,
    WebhookEvent::DocumentUploaded->value,
];
```

## Event Frequency

| Event | Typical Frequency |
|-------|-------------------|
| `vehicle.location.updated` | Every 30-60 seconds per vehicle |
| `driver.status.changed` | Several times per day per driver |
| `hos.violation.detected` | Varies by compliance |
| `dispatch.status.changed` | Per dispatch lifecycle |
| `geofence.entered/exited` | Per geofence crossing |
| `safety.event.detected` | Varies by driving behavior |
| `inspection.completed` | 2x per day per driver |

## Best Practices

1. **Filter events** - Only subscribe to events you need
2. **High-volume events** - Consider queuing `vehicle.location.updated`
3. **Critical events** - Process `hos.violation.detected` synchronously
4. **Aggregate events** - Batch location updates for analytics

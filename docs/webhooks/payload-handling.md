# Payload Handling

Learn how to parse and process webhook payloads using the SDK's type-safe utilities.

## WebhookPayload Class

The `WebhookPayload` class provides a structured way to access webhook data:

```php
use Motive\Webhooks\WebhookPayload;

$payload = WebhookPayload::fromRequest($request);

echo $payload->event;     // WebhookEvent::VehicleLocationUpdated
echo $payload->timestamp; // CarbonImmutable instance
echo $payload->data;      // Array of event-specific data
```

## Parsing Payloads

### From HTTP Request

```php
use Illuminate\Http\Request;
use Motive\Webhooks\WebhookPayload;

public function handle(Request $request)
{
    $payload = WebhookPayload::fromRequest($request);

    // Process the payload
}
```

### From Array

```php
use Motive\Webhooks\WebhookPayload;

$data = [
    'event' => 'vehicle.location.updated',
    'timestamp' => '2024-01-15T10:30:00Z',
    'data' => [
        'vehicle_id' => 123,
        'latitude' => 32.7767,
        'longitude' => -96.7970,
    ],
];

$payload = WebhookPayload::from($data);
```

## Payload Properties

| Property | Type | Description |
|----------|------|-------------|
| `event` | `WebhookEvent` | The event type enum |
| `timestamp` | `CarbonImmutable` | When the event occurred |
| `data` | `array` | Event-specific data |

## Handling Different Events

### Using Match Expression

```php
use Motive\Enums\WebhookEvent;

public function handle(Request $request)
{
    $payload = WebhookPayload::fromRequest($request);

    match ($payload->event) {
        WebhookEvent::VehicleLocationUpdated => $this->handleLocationUpdate($payload),
        WebhookEvent::VehicleCreated => $this->handleVehicleCreated($payload),
        WebhookEvent::VehicleUpdated => $this->handleVehicleUpdated($payload),
        WebhookEvent::VehicleDeleted => $this->handleVehicleDeleted($payload),
        WebhookEvent::DriverStatusChanged => $this->handleStatusChange($payload),
        WebhookEvent::HosViolationDetected => $this->handleViolation($payload),
        WebhookEvent::DispatchCreated => $this->handleDispatchCreated($payload),
        WebhookEvent::DispatchStatusChanged => $this->handleDispatchUpdate($payload),
        WebhookEvent::GeofenceEntered => $this->handleGeofenceEntry($payload),
        WebhookEvent::GeofenceExited => $this->handleGeofenceExit($payload),
        WebhookEvent::SafetyEventDetected => $this->handleSafetyEvent($payload),
        WebhookEvent::InspectionCompleted => $this->handleInspection($payload),
        WebhookEvent::DocumentUploaded => $this->handleDocument($payload),
        default => $this->logUnhandledEvent($payload),
    };

    return response()->json(['status' => 'ok']);
}
```

### Event-Specific Handlers

```php
protected function handleLocationUpdate(WebhookPayload $payload): void
{
    $data = $payload->data;

    Vehicle::where('motive_id', $data['vehicle_id'])->update([
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'heading' => $data['heading'] ?? null,
        'speed' => $data['speed'] ?? null,
        'location_updated_at' => $payload->timestamp,
    ]);
}

protected function handleStatusChange(WebhookPayload $payload): void
{
    $data = $payload->data;

    $driver = Driver::where('motive_id', $data['driver_id'])->first();

    $driver->update([
        'duty_status' => $data['status'],
        'status_changed_at' => $payload->timestamp,
    ]);

    event(new DriverStatusChanged($driver, $data['status']));
}

protected function handleViolation(WebhookPayload $payload): void
{
    $data = $payload->data;

    $violation = HosViolation::create([
        'driver_id' => $data['driver_id'],
        'type' => $data['violation_type'],
        'detected_at' => $payload->timestamp,
        'details' => $data,
    ]);

    Notification::send(
        User::role('safety_manager')->get(),
        new HosViolationAlert($violation)
    );
}
```

## Accessing Raw Data

Get the original payload if needed:

```php
$payload = WebhookPayload::fromRequest($request);

// Parsed data
$eventData = $payload->data;

// Original raw JSON
$rawData = $payload->raw();
```

## Converting to Array

```php
$payload = WebhookPayload::fromRequest($request);

$array = $payload->toArray();
// [
//     'event' => 'vehicle.location.updated',
//     'timestamp' => '2024-01-15T10:30:00+00:00',
//     'data' => [...],
// ]
```

## Queueing Webhook Processing

For better performance, queue webhook processing:

```php
public function handle(Request $request)
{
    $payload = WebhookPayload::fromRequest($request);

    // Dispatch to queue immediately
    ProcessMotiveWebhook::dispatch($payload->toArray());

    return response()->json(['status' => 'accepted'], 202);
}
```

```php
// app/Jobs/ProcessMotiveWebhook.php
class ProcessMotiveWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $payloadData
    ) {}

    public function handle(): void
    {
        $payload = WebhookPayload::from($this->payloadData);

        // Process the webhook
        match ($payload->event) {
            // ... handle events
        };
    }
}
```

## Idempotency

Handle duplicate webhook deliveries:

```php
public function handle(Request $request)
{
    $payload = WebhookPayload::fromRequest($request);

    // Create unique key from event data
    $idempotencyKey = md5(json_encode([
        $payload->event->value,
        $payload->timestamp->timestamp,
        $payload->data['id'] ?? null,
    ]));

    // Check if already processed
    if (Cache::has("webhook:{$idempotencyKey}")) {
        return response()->json(['status' => 'already_processed']);
    }

    // Mark as processing
    Cache::put("webhook:{$idempotencyKey}", true, now()->addHours(24));

    // Process the webhook
    $this->processPayload($payload);

    return response()->json(['status' => 'ok']);
}
```

## Error Handling

```php
public function handle(Request $request)
{
    try {
        $payload = WebhookPayload::fromRequest($request);
        $this->processPayload($payload);

        return response()->json(['status' => 'ok']);
    } catch (\InvalidArgumentException $e) {
        // Invalid event type or malformed payload
        Log::warning('Invalid webhook payload', [
            'error' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json(['error' => 'Invalid payload'], 400);
    } catch (\Throwable $e) {
        // Processing error - Motive will retry
        Log::error('Webhook processing failed', [
            'error' => $e->getMessage(),
            'payload' => $request->all(),
        ]);

        return response()->json(['error' => 'Processing failed'], 500);
    }
}
```

## Logging Webhooks

```php
public function handle(Request $request)
{
    $payload = WebhookPayload::fromRequest($request);

    // Log for debugging/auditing
    Log::info('Webhook received', [
        'event' => $payload->event->value,
        'timestamp' => $payload->timestamp->toIso8601String(),
        'data_keys' => array_keys($payload->data),
    ]);

    $this->processPayload($payload);

    return response()->json(['status' => 'ok']);
}
```

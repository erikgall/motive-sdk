# Webhooks

The Webhooks resource manages webhook subscriptions for real-time event notifications.

## Access

```php
use Motive\Facades\Motive;

$webhooks = Motive::webhooks();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List webhooks |
| `find($id)` | Find webhook by ID |
| `create($data)` | Create a webhook |
| `update($id, $data)` | Update a webhook |
| `delete($id)` | Delete a webhook |
| `test($id)` | Send test event |
| `logs($id, $params)` | Get delivery logs |

## List Webhooks

```php
$webhooks = Motive::webhooks()->list();

foreach ($webhooks as $webhook) {
    echo "URL: {$webhook->url}\n";
    echo "Events: " . implode(', ', $webhook->events) . "\n";
    echo "Status: {$webhook->status}\n";
}
```

## Find a Webhook

```php
$webhook = Motive::webhooks()->find($webhookId);

echo $webhook->url;
echo $webhook->status;
```

## Create a Webhook

```php
use Motive\Enums\WebhookEvent;

$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::HosViolationDetected->value,
        WebhookEvent::DispatchStatusChanged->value,
    ],
    'secret' => 'your-webhook-secret',
]);
```

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `url` | string | Yes | Webhook endpoint URL |
| `events` | array | Yes | Events to subscribe to |
| `secret` | string | No | Signing secret |
| `active` | bool | No | Whether webhook is active |

## Update a Webhook

```php
$webhook = Motive::webhooks()->update($webhookId, [
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
    ],
    'active' => false,
]);
```

## Delete a Webhook

```php
$deleted = Motive::webhooks()->delete($webhookId);
```

## Test a Webhook

Send a test event to verify your endpoint:

```php
$result = Motive::webhooks()->test($webhookId);

echo "Status: {$result->status}";
echo "Response: {$result->responseCode}";
```

## Get Delivery Logs

```php
$logs = Motive::webhooks()->logs($webhookId, [
    'start_date' => now()->subDays(7)->toDateString(),
]);

foreach ($logs as $log) {
    echo "Event: {$log->event}\n";
    echo "Sent: {$log->sentAt}\n";
    echo "Status: {$log->responseCode}\n";
    echo "Success: " . ($log->success ? 'Yes' : 'No') . "\n";
}
```

## Webhook DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Webhook ID |
| `url` | string | Endpoint URL |
| `events` | array | Subscribed events |
| `status` | WebhookStatus | Current status |
| `secret` | string\|null | Signing secret |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## WebhookLog DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Log ID |
| `webhookId` | int | Webhook ID |
| `event` | string | Event type |
| `payload` | array\|null | Event payload |
| `responseCode` | int\|null | HTTP response code |
| `responseBody` | string\|null | Response body |
| `success` | bool | Delivery success |
| `sentAt` | CarbonImmutable | Send timestamp |

## WebhookStatus Enum

| Value | Description |
|-------|-------------|
| `active` | Webhook is active |
| `inactive` | Webhook is disabled |
| `failing` | Multiple delivery failures |

## Use Cases

### Setup All Event Types

```php
use Motive\Enums\WebhookEvent;

$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::VehicleCreated->value,
        WebhookEvent::DriverStatusChanged->value,
        WebhookEvent::HosViolationDetected->value,
        WebhookEvent::DispatchStatusChanged->value,
        WebhookEvent::GeofenceEntered->value,
        WebhookEvent::GeofenceExited->value,
    ],
    'secret' => config('motive.webhooks.secret'),
]);
```

### Monitor Webhook Health

```php
$webhooks = Motive::webhooks()->list();

foreach ($webhooks as $webhook) {
    $logs = Motive::webhooks()->logs($webhook->id, [
        'start_date' => now()->subDay()->toDateString(),
    ]);

    $failed = collect($logs)->where('success', false)->count();
    $total = count($logs);

    if ($total > 0) {
        $successRate = (($total - $failed) / $total) * 100;
        echo "{$webhook->url}: {$successRate}% success rate\n";

        if ($successRate < 90) {
            Log::warning('Webhook reliability issue', [
                'webhook_id' => $webhook->id,
                'success_rate' => $successRate,
            ]);
        }
    }
}
```

## Related

- [Webhook Event Types](../../webhooks/event-types.md)
- [Signature Verification](../../webhooks/signature-verification.md)
- [Payload Handling](../../webhooks/payload-handling.md)

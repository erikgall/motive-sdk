# Webhook Registration

Learn how to create, manage, and monitor webhook subscriptions.

## Creating Webhooks

### Basic Registration

```php
use Motive\Facades\Motive;
use Motive\Enums\WebhookEvent;

$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::DriverStatusChanged->value,
    ],
]);

// The response includes a secret for signature verification
echo $webhook->id;        // 123
echo $webhook->secret;    // 'whsec_...'
echo $webhook->status;    // WebhookStatus::Active
```

### With Description

```php
$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/fleet',
    'events' => [
        WebhookEvent::VehicleCreated->value,
        WebhookEvent::VehicleUpdated->value,
        WebhookEvent::VehicleDeleted->value,
    ],
    'description' => 'Fleet management integration',
]);
```

### Multiple Event Types

```php
$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/compliance',
    'events' => [
        WebhookEvent::HosViolationDetected->value,
        WebhookEvent::DriverStatusChanged->value,
        WebhookEvent::InspectionCompleted->value,
        WebhookEvent::SafetyEventDetected->value,
    ],
    'description' => 'Compliance monitoring',
]);
```

## Managing Webhooks

### List All Webhooks

```php
$webhooks = Motive::webhooks()->list();

foreach ($webhooks as $webhook) {
    echo "{$webhook->id}: {$webhook->url} ({$webhook->status->value})\n";
}
```

### Find a Webhook

```php
$webhook = Motive::webhooks()->find(123);

echo $webhook->url;
echo $webhook->description;

// List subscribed events
foreach ($webhook->events as $event) {
    echo $event->value . "\n";
}
```

### Update a Webhook

```php
$webhook = Motive::webhooks()->update(123, [
    'url' => 'https://your-app.com/webhooks/v2/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::GeofenceEntered->value,
        WebhookEvent::GeofenceExited->value,
    ],
]);
```

### Delete a Webhook

```php
Motive::webhooks()->delete(123);
```

## Testing Webhooks

### Send Test Payload

Send a test webhook to verify your endpoint is working:

```php
$success = Motive::webhooks()->test(123);

if ($success) {
    echo "Test webhook sent successfully";
}
```

## Monitoring Deliveries

### View Delivery Logs

Check the delivery history for a webhook:

```php
$logs = Motive::webhooks()->logs(123);

foreach ($logs as $log) {
    echo "Event: {$log->event}\n";
    echo "Status: {$log->responseStatus}\n";
    echo "Delivered: {$log->deliveredAt}\n";
    echo "---\n";
}
```

### Check Webhook Status

```php
use Motive\Enums\WebhookStatus;

$webhook = Motive::webhooks()->find(123);

match ($webhook->status) {
    WebhookStatus::Active => 'Webhook is receiving events',
    WebhookStatus::Inactive => 'Webhook is disabled',
    WebhookStatus::Failing => 'Webhook has delivery failures',
};
```

## Webhook Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Unique identifier |
| `companyId` | `int` | Company ID |
| `url` | `string` | Delivery URL (HTTPS required) |
| `events` | `array<WebhookEvent>` | Subscribed events |
| `status` | `WebhookStatus` | Current status |
| `secret` | `string\|null` | Signing secret (only on create) |
| `description` | `string\|null` | Optional description |
| `createdAt` | `CarbonImmutable` | Creation timestamp |
| `updatedAt` | `CarbonImmutable` | Last update timestamp |

## Storing the Secret

The webhook secret is only returned when creating a webhook. Store it securely:

```php
// Create webhook and get secret
$webhook = Motive::webhooks()->create([...]);

// Store in environment or secure config
// MOTIVE_WEBHOOK_SECRET=whsec_...

// Or in your database (encrypted)
$tenant->update([
    'motive_webhook_secret' => encrypt($webhook->secret),
]);
```

## Rate Limits

Motive has rate limits on webhook deliveries:

- Maximum 1000 events per minute per webhook
- Failed deliveries are retried with exponential backoff
- Webhooks are automatically disabled after repeated failures

## Best Practices

1. **Store secrets securely** - Never commit secrets to version control
2. **Use descriptive names** - Add descriptions to identify webhook purpose
3. **Subscribe selectively** - Only subscribe to events you need
4. **Monitor logs regularly** - Check for delivery failures
5. **Handle failures gracefully** - Implement retry logic in your handler

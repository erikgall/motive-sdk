# Webhooks

Webhooks allow your application to receive real-time notifications when events occur in the Motive platform. This eliminates the need for polling and enables immediate responses to important events.

## Overview

The Motive SDK provides comprehensive webhook support including:

- **Webhook Registration** - Create and manage webhook subscriptions
- **Signature Verification** - Secure your endpoints with HMAC signature verification
- **Payload Handling** - Parse and process webhook payloads with type-safe objects
- **Event Types** - Subscribe to specific events relevant to your integration

## Quick Start

### 1. Register a Webhook

```php
use Motive\Facades\Motive;
use Motive\Enums\WebhookEvent;

$webhook = Motive::webhooks()->create([
    'url' => 'https://your-app.com/webhooks/motive',
    'events' => [
        WebhookEvent::VehicleLocationUpdated->value,
        WebhookEvent::DriverStatusChanged->value,
        WebhookEvent::HosViolationDetected->value,
    ],
    'description' => 'Fleet monitoring webhook',
]);

// Store the secret for signature verification
$secret = $webhook->secret;
```

### 2. Set Up Your Endpoint

```php
// routes/web.php
use App\Http\Controllers\WebhookController;

Route::post('/webhooks/motive', [WebhookController::class, 'handle'])
    ->middleware('motive.webhook');
```

### 3. Register the Middleware

```php
// bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'motive.webhook' => \Motive\Http\Middleware\VerifyWebhookSignature::class,
    ]);
})
```

### 4. Handle the Webhook

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Motive\Webhooks\WebhookPayload;
use Motive\Enums\WebhookEvent;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = WebhookPayload::fromRequest($request);

        match ($payload->event) {
            WebhookEvent::VehicleLocationUpdated => $this->handleLocationUpdate($payload),
            WebhookEvent::DriverStatusChanged => $this->handleStatusChange($payload),
            WebhookEvent::HosViolationDetected => $this->handleViolation($payload),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }
}
```

## Available Event Types

| Event | Description |
|-------|-------------|
| `vehicle.location.updated` | Vehicle position changed |
| `vehicle.created` | New vehicle added |
| `vehicle.updated` | Vehicle data updated |
| `vehicle.deleted` | Vehicle removed |
| `driver.status.changed` | Driver duty status changed |
| `hos.violation.detected` | HOS violation occurred |
| `dispatch.created` | New dispatch created |
| `dispatch.status.changed` | Dispatch status updated |
| `geofence.entered` | Vehicle entered geofence |
| `geofence.exited` | Vehicle exited geofence |
| `safety.event.detected` | Safety event occurred |
| `inspection.completed` | DVIR completed |
| `document.uploaded` | Document uploaded |

## Documentation

- [Registration](webhooks/registration.md) - Create and manage webhooks
- [Signature Verification](webhooks/signature-verification.md) - Secure your endpoints
- [Payload Handling](webhooks/payload-handling.md) - Parse webhook data
- [Event Types](webhooks/event-types.md) - Available webhook events

## Best Practices

1. **Always verify signatures** - Use the middleware to prevent spoofed requests
2. **Respond quickly** - Return a 200 response immediately, then process async
3. **Handle duplicates** - Webhooks may be retried; implement idempotency
4. **Monitor delivery** - Check webhook logs for failed deliveries
5. **Use HTTPS** - Webhook URLs must use HTTPS in production

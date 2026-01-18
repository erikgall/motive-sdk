# Signature Verification

Protect your webhook endpoints from spoofed requests by verifying the cryptographic signature included with each webhook delivery.

## How It Works

When Motive sends a webhook, it includes two headers:

- `X-Motive-Signature` - HMAC-SHA256 signature of the payload
- `X-Motive-Timestamp` - Unix timestamp when the webhook was sent

The SDK provides middleware and utilities to verify these signatures automatically.

## Using the Middleware

### Register the Middleware

```php
// bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'motive.webhook' => \Motive\Http\Middleware\VerifyWebhookSignature::class,
    ]);
})
```

For Laravel 10 and earlier:

```php
// app/Http/Kernel.php
protected $middlewareAliases = [
    'motive.webhook' => \Motive\Http\Middleware\VerifyWebhookSignature::class,
];
```

### Apply to Routes

```php
// routes/web.php
Route::post('/webhooks/motive', [WebhookController::class, 'handle'])
    ->middleware('motive.webhook');
```

### Configure the Secret

The middleware reads the secret from your configuration:

```php
// config/motive.php
return [
    'webhook_secret' => env('MOTIVE_WEBHOOK_SECRET'),
    'webhook_tolerance' => 300, // 5 minutes (optional)
];
```

Set the environment variable:

```env
MOTIVE_WEBHOOK_SECRET=whsec_your_secret_here
```

## Manual Verification

For custom verification logic, use the `WebhookSignature` class directly:

### Basic Verification

```php
use Motive\Webhooks\WebhookSignature;

$payload = $request->getContent();
$signature = $request->header('X-Motive-Signature');
$secret = config('motive.webhook_secret');

if (WebhookSignature::verify($payload, $signature, $secret)) {
    // Signature is valid
    $this->processWebhook($request);
} else {
    abort(403, 'Invalid webhook signature');
}
```

### Verification with Timestamp

Include timestamp validation to prevent replay attacks:

```php
use Motive\Webhooks\WebhookSignature;

$payload = $request->getContent();
$signature = $request->header('X-Motive-Signature');
$timestamp = (int) $request->header('X-Motive-Timestamp');
$secret = config('motive.webhook_secret');
$tolerance = 300; // 5 minutes

$isValid = WebhookSignature::verifyWithTimestamp(
    payload: $payload,
    signature: $signature,
    secret: $secret,
    timestamp: $timestamp,
    tolerance: $tolerance
);

if (!$isValid) {
    abort(403, 'Invalid or expired webhook signature');
}
```

## Generating Signatures

For testing purposes, you can generate signatures:

```php
use Motive\Webhooks\WebhookSignature;

$payload = json_encode(['event' => 'vehicle.updated', 'data' => [...]]);
$secret = 'your_webhook_secret';

// Simple signature
$signature = WebhookSignature::generate($payload, $secret);

// Signature with timestamp
$timestamp = time();
$signature = WebhookSignature::generateWithTimestamp($payload, $secret, $timestamp);
```

## Exception Handling

The middleware throws `WebhookVerificationException` for verification failures:

```php
use Motive\Exceptions\WebhookVerificationException;

try {
    // Process webhook
} catch (WebhookVerificationException $e) {
    match ($e->getCode()) {
        WebhookVerificationException::MISSING_SIGNATURE => 'No signature header',
        WebhookVerificationException::INVALID_SIGNATURE => 'Signature mismatch',
        WebhookVerificationException::EXPIRED_TIMESTAMP => 'Timestamp too old',
        default => 'Verification failed',
    };
}
```

## Custom Middleware

Create a custom middleware for advanced scenarios:

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Motive\Webhooks\WebhookSignature;
use Motive\Exceptions\WebhookVerificationException;

class VerifyMotiveWebhook
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Motive-Signature');
        $timestamp = $request->header('X-Motive-Timestamp');
        $payload = $request->getContent();

        if (!$signature) {
            throw WebhookVerificationException::missingSignature();
        }

        // Get secret based on tenant or other logic
        $secret = $this->getWebhookSecret($request);

        if ($timestamp) {
            $isValid = WebhookSignature::verifyWithTimestamp(
                $payload,
                $signature,
                $secret,
                (int) $timestamp,
                300
            );
        } else {
            $isValid = WebhookSignature::verify($payload, $signature, $secret);
        }

        if (!$isValid) {
            throw WebhookVerificationException::invalidSignature();
        }

        return $next($request);
    }

    protected function getWebhookSecret(Request $request): string
    {
        // Multi-tenant: get secret from database
        $tenantId = $request->header('X-Tenant-Id');
        return Tenant::find($tenantId)->motive_webhook_secret;
    }
}
```

## Testing with Signatures

When testing your webhook handler, generate valid signatures:

```php
public function test_webhook_processes_vehicle_update()
{
    $payload = json_encode([
        'event' => 'vehicle.location.updated',
        'timestamp' => now()->toIso8601String(),
        'data' => ['vehicle_id' => 123, 'latitude' => 32.7767],
    ]);

    $secret = config('motive.webhook_secret');
    $timestamp = time();
    $signature = WebhookSignature::generateWithTimestamp($payload, $secret, $timestamp);

    $response = $this->postJson('/webhooks/motive', json_decode($payload, true), [
        'X-Motive-Signature' => $signature,
        'X-Motive-Timestamp' => $timestamp,
    ]);

    $response->assertOk();
}
```

## Security Considerations

1. **Use HTTPS** - Always use HTTPS for webhook endpoints
2. **Validate timestamps** - Reject webhooks with old timestamps (replay protection)
3. **Store secrets securely** - Use environment variables or encrypted storage
4. **Rotate secrets periodically** - Update webhook secrets on a regular schedule
5. **Log verification failures** - Monitor for potential attacks

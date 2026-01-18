# Error Handling

The Motive SDK converts API errors into typed exceptions, making it easy to handle different error conditions appropriately.

## Exception Hierarchy

All SDK exceptions extend the base `MotiveException`:

```
MotiveException
├── AuthenticationException (401)
├── AuthorizationException (403)
├── NotFoundException (404)
├── ValidationException (422)
├── RateLimitException (429)
├── ServerException (5xx)
└── WebhookVerificationException
```

## Handling Exceptions

### Comprehensive Error Handling

```php
use Motive\Exceptions\MotiveException;
use Motive\Exceptions\AuthenticationException;
use Motive\Exceptions\AuthorizationException;
use Motive\Exceptions\ValidationException;
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\RateLimitException;
use Motive\Exceptions\ServerException;

try {
    $vehicle = Motive::vehicles()->find(123);
} catch (NotFoundException $e) {
    // Resource not found (404)
    Log::warning("Vehicle not found: {$e->getMessage()}");
    return response()->json(['error' => 'Vehicle not found'], 404);

} catch (AuthenticationException $e) {
    // Invalid or expired credentials (401)
    Log::error("Authentication failed: {$e->getMessage()}");
    throw $e;

} catch (AuthorizationException $e) {
    // Insufficient permissions (403)
    Log::warning("Access denied: {$e->getMessage()}");
    return response()->json(['error' => 'Access denied'], 403);

} catch (ValidationException $e) {
    // Invalid request data (422)
    return response()->json([
        'error' => 'Validation failed',
        'errors' => $e->errors(),
    ], 422);

} catch (RateLimitException $e) {
    // Too many requests (429)
    Log::warning("Rate limited, retry after {$e->retryAfter()} seconds");
    return response()
        ->json(['error' => 'Too many requests'], 429)
        ->header('Retry-After', $e->retryAfter());

} catch (ServerException $e) {
    // Motive server error (5xx)
    Log::error("Motive API error: {$e->getMessage()}");
    return response()->json(['error' => 'Service unavailable'], 503);

} catch (MotiveException $e) {
    // Any other Motive API error
    Log::error("API error: {$e->getMessage()}", [
        'code' => $e->getCode(),
        'body' => $e->getResponseBody(),
    ]);
    throw $e;
}
```

### Simple Error Handling

For simpler cases, catch the base exception:

```php
try {
    $vehicle = Motive::vehicles()->find(123);
} catch (MotiveException $e) {
    Log::error("Motive API error: {$e->getMessage()}");
    return null;
}
```

## Exception Types

### AuthenticationException (401)

Thrown when authentication fails:

```php
try {
    $vehicles = Motive::withApiKey('invalid-key')->vehicles()->list();
} catch (AuthenticationException $e) {
    echo "Invalid API key";
}
```

**Common causes:**
- Invalid API key
- Expired OAuth token
- Missing authentication credentials

### AuthorizationException (403)

Thrown when the authenticated user lacks permission:

```php
try {
    $vehicle = Motive::vehicles()->delete(123);
} catch (AuthorizationException $e) {
    echo "You don't have permission to delete vehicles";
}
```

**Common causes:**
- Insufficient API scopes
- Resource belongs to another organization
- Action not allowed for user role

### NotFoundException (404)

Thrown when a resource doesn't exist:

```php
try {
    $vehicle = Motive::vehicles()->find(99999);
} catch (NotFoundException $e) {
    echo "Vehicle not found";
}
```

### ValidationException (422)

Thrown when request data is invalid:

```php
try {
    $vehicle = Motive::vehicles()->create([
        'number' => '', // Invalid: empty
    ]);
} catch (ValidationException $e) {
    // Get all validation errors
    $errors = $e->errors();
    // ['number' => ['Vehicle number is required']]

    foreach ($errors as $field => $messages) {
        echo "{$field}: " . implode(', ', $messages);
    }
}
```

### RateLimitException (429)

Thrown when API rate limits are exceeded:

```php
try {
    // Many rapid requests...
} catch (RateLimitException $e) {
    $retryAfter = $e->retryAfter(); // Seconds until retry is allowed
    sleep($retryAfter);
    // Retry the request
}
```

### ServerException (5xx)

Thrown when the Motive API has an internal error:

```php
try {
    $vehicles = Motive::vehicles()->list();
} catch (ServerException $e) {
    // Log and retry later
    Log::error("Motive API is down: {$e->getMessage()}");
}
```

### WebhookVerificationException

Thrown when webhook signature verification fails:

```php
use Motive\Exceptions\WebhookVerificationException;

try {
    $payload = WebhookPayload::fromRequest($request);
} catch (WebhookVerificationException $e) {
    Log::warning("Invalid webhook signature");
    return response('Invalid signature', 400);
}
```

## Accessing Response Details

All exceptions provide access to response information:

```php
try {
    $vehicle = Motive::vehicles()->create([...]);
} catch (MotiveException $e) {
    // HTTP status code
    $statusCode = $e->getCode();

    // Error message
    $message = $e->getMessage();

    // Full response body (decoded JSON)
    $body = $e->getResponseBody();

    // Original response object
    $response = $e->getResponse();
}
```

## Automatic Retries

The SDK automatically retries failed requests for transient errors:

- Network timeouts
- 5xx server errors

Configure retry behavior in `config/motive.php`:

```php
'connections' => [
    'default' => [
        'retry' => [
            'times' => 3,      // Number of retries
            'sleep' => 100,    // Milliseconds between retries
        ],
    ],
],
```

## Global Exception Handling

Register a global handler in your Laravel exception handler:

```php
// app/Exceptions/Handler.php
use Motive\Exceptions\MotiveException;
use Motive\Exceptions\RateLimitException;

public function register(): void
{
    $this->renderable(function (RateLimitException $e, Request $request) {
        return response()->json([
            'error' => 'Rate limit exceeded',
            'retry_after' => $e->retryAfter(),
        ], 429);
    });

    $this->renderable(function (MotiveException $e, Request $request) {
        return response()->json([
            'error' => $e->getMessage(),
        ], $e->getCode() ?: 500);
    });
}
```

# Multi-Tenancy

The Motive SDK supports multi-tenant applications where you need to access multiple Motive accounts from a single application.

## Named Connections

Configure multiple connections in `config/motive.php`:

```php
return [
    'default' => 'default',

    'connections' => [
        'default' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_API_KEY'),
        ],
        'company-a' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_COMPANY_A_API_KEY'),
        ],
        'company-b' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_COMPANY_B_API_KEY'),
        ],
    ],
];
```

### Using Named Connections

```php
use Motive\Facades\Motive;

// Use default connection
$vehicles = Motive::vehicles()->list();

// Use specific connection
$vehiclesA = Motive::connection('company-a')->vehicles()->list();
$vehiclesB = Motive::connection('company-b')->vehicles()->list();
```

## Dynamic API Keys

For dynamic multi-tenancy (e.g., storing API keys in the database), use `withApiKey()`:

```php
// Get tenant's API key from database
$tenant = Tenant::find($tenantId);

$vehicles = Motive::withApiKey($tenant->motive_api_key)
    ->vehicles()
    ->list();
```

## OAuth Multi-Tenancy

For OAuth-based multi-tenancy:

```php
// Each user has their own tokens
$user = auth()->user();

$vehicles = Motive::withOAuth(
    accessToken: $user->motive_access_token,
    refreshToken: $user->motive_refresh_token,
    expiresAt: $user->motive_token_expires_at,
)->vehicles()->list();
```

### With Token Store

```php
use App\Services\DatabaseTokenStore;

$tokenStore = new DatabaseTokenStore($user);

$vehicles = Motive::withTokenStore($tokenStore)
    ->vehicles()
    ->list();
```

## Tenant-Scoped Service

Create a service that resolves the correct Motive instance per tenant:

```php
<?php

namespace App\Services;

use App\Models\Tenant;
use Motive\Facades\Motive;
use Motive\MotiveManager;

class TenantMotiveService
{
    public function __construct(
        protected MotiveManager $motive
    ) {}

    public function forTenant(Tenant $tenant): MotiveManager
    {
        if ($tenant->motive_auth_type === 'api_key') {
            return $this->motive->withApiKey($tenant->motive_api_key);
        }

        return $this->motive->withOAuth(
            $tenant->motive_access_token,
            $tenant->motive_refresh_token,
            $tenant->motive_token_expires_at,
        );
    }
}
```

### Usage

```php
class VehicleController extends Controller
{
    public function __construct(
        protected TenantMotiveService $motiveService
    ) {}

    public function index(Tenant $tenant)
    {
        $motive = $this->motiveService->forTenant($tenant);

        return $motive->vehicles()->list();
    }
}
```

## Context-Aware Multi-Tenancy

Combine connections with context modifiers:

```php
// Each tenant gets their own connection + settings
$vehicles = Motive::connection('company-a')
    ->withTimezone($companyA->timezone)
    ->withMetricUnits($companyA->uses_metric)
    ->vehicles()
    ->list();
```

## Middleware Approach

Use middleware to set the Motive connection per request:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Motive\MotiveManager;

class SetMotiveConnection
{
    public function __construct(
        protected MotiveManager $motive
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $tenant = $request->route('tenant');

        if ($tenant && $tenant->motive_api_key) {
            app()->instance(
                MotiveManager::class,
                $this->motive->withApiKey($tenant->motive_api_key)
            );
        }

        return $next($request);
    }
}
```

Register the middleware:

```php
// app/Http/Kernel.php
protected $routeMiddleware = [
    'motive.tenant' => SetMotiveConnection::class,
];
```

Use in routes:

```php
Route::middleware('motive.tenant')->group(function () {
    Route::get('/tenants/{tenant}/vehicles', [VehicleController::class, 'index']);
});
```

## Database Schema

For multi-tenant applications, store Motive credentials per tenant:

```php
Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->string('name');

    // API key authentication
    $table->text('motive_api_key')->nullable();

    // OAuth authentication
    $table->text('motive_access_token')->nullable();
    $table->text('motive_refresh_token')->nullable();
    $table->timestamp('motive_token_expires_at')->nullable();

    // Settings
    $table->string('motive_timezone')->default('UTC');
    $table->boolean('motive_uses_metric')->default(false);

    $table->timestamps();
});
```

## Caching Per Tenant

Cache API responses per tenant:

```php
use Illuminate\Support\Facades\Cache;

class VehicleService
{
    public function getVehicles(Tenant $tenant): array
    {
        $cacheKey = "tenant:{$tenant->id}:vehicles";

        return Cache::remember($cacheKey, 300, function () use ($tenant) {
            return Motive::withApiKey($tenant->motive_api_key)
                ->vehicles()
                ->list()
                ->all();
        });
    }
}
```

## Rate Limiting Per Tenant

Implement per-tenant rate limiting:

```php
use Illuminate\Support\Facades\RateLimiter;

class TenantMotiveService
{
    public function forTenant(Tenant $tenant): MotiveManager
    {
        $key = "motive-api:{$tenant->id}";

        if (RateLimiter::tooManyAttempts($key, 100)) {
            throw new TooManyRequestsException(
                'Tenant rate limit exceeded'
            );
        }

        RateLimiter::hit($key, 60);

        return $this->motive->withApiKey($tenant->motive_api_key);
    }
}
```

## Best Practices

1. **Isolate tenant data**: Never mix data between tenants
2. **Validate tenant access**: Always verify the user can access the tenant
3. **Use encryption**: Encrypt API keys and tokens in the database
4. **Implement connection pooling**: Reuse connections where possible
5. **Monitor per-tenant usage**: Track API calls per tenant for billing/limits
6. **Handle disconnections gracefully**: Provide UI for reconnecting expired OAuth tokens

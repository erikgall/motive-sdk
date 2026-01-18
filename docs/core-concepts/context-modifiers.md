# Context Modifiers

Context modifiers allow you to customize API request behavior by setting headers that affect how the Motive API processes and returns data.

## Available Modifiers

### Timezone

Set the timezone for all datetime values in responses:

```php
use Motive\Facades\Motive;

$logs = Motive::withTimezone('America/Chicago')
    ->hosLogs()
    ->list();

// All timestamps in $logs are in America/Chicago timezone
```

**Supported timezones:**
- Any valid timezone identifier (e.g., `America/New_York`, `Europe/London`, `Asia/Tokyo`)
- UTC is used if no timezone is specified

### Metric Units

Enable metric units for distances and volumes:

```php
// Distances in kilometers, volumes in liters
$vehicles = Motive::withMetricUnits()
    ->vehicles()
    ->list();

// Explicitly disable (use imperial)
$vehicles = Motive::withMetricUnits(false)
    ->vehicles()
    ->list();
```

**Affected values:**
- Distances: kilometers instead of miles
- Volumes: liters instead of gallons
- Speed: km/h instead of mph

### User Context

Set the user ID for audit trails:

```php
// Include X-User-Id header for auditing
$dispatch = Motive::withUserId($currentUser->id)
    ->dispatches()
    ->create([...]);
```

This helps track which user initiated API actions in your audit logs.

## Chaining Modifiers

Multiple modifiers can be chained together:

```php
$vehicles = Motive::connection('company-a')
    ->withTimezone('America/Los_Angeles')
    ->withMetricUnits()
    ->withUserId($user->id)
    ->vehicles()
    ->list();
```

The order of chaining doesn't matter - all modifiers are applied to the request.

## Modifier Scope

Modifiers apply to the specific instance and all subsequent calls on that instance:

```php
// Create a configured instance
$motiveLA = Motive::withTimezone('America/Los_Angeles');

// Both calls use LA timezone
$vehicles = $motiveLA->vehicles()->list();
$users = $motiveLA->users()->list();

// Original facade is unaffected
$defaultVehicles = Motive::vehicles()->list(); // Uses default/UTC timezone
```

## Configuration-Based Defaults

Set default values in `config/motive.php`:

```php
'headers' => [
    'timezone' => env('MOTIVE_TIMEZONE', 'America/Chicago'),
    'metric_units' => env('MOTIVE_METRIC_UNITS', false),
],
```

Then in your `.env`:

```env
MOTIVE_TIMEZONE=America/Chicago
MOTIVE_METRIC_UNITS=false
```

These defaults apply to all requests unless overridden with context modifiers.

## Connection-Specific Modifiers

Combine with named connections for tenant-specific settings:

```php
// Each tenant gets their own timezone
$tenantA = Motive::connection('tenant-a')
    ->withTimezone('America/New_York');

$tenantB = Motive::connection('tenant-b')
    ->withTimezone('Europe/London')
    ->withMetricUnits();
```

## Use Cases

### Multi-Timezone Support

Display times in the user's local timezone:

```php
class HosController extends Controller
{
    public function index(Request $request)
    {
        $userTimezone = $request->user()->timezone;

        $logs = Motive::withTimezone($userTimezone)
            ->hosLogs()
            ->list(['driver_id' => $request->driver_id]);

        return view('hos.index', compact('logs'));
    }
}
```

### International Fleet Management

Handle international fleets with metric units:

```php
$euVehicles = Motive::connection('europe')
    ->withMetricUnits()
    ->withTimezone('Europe/Paris')
    ->vehicles()
    ->list();

$usVehicles = Motive::connection('usa')
    ->withTimezone('America/Chicago')
    ->vehicles()
    ->list();
```

### Audit Trail Integration

Track user actions for compliance:

```php
class DispatchController extends Controller
{
    public function store(DispatchRequest $request)
    {
        $dispatch = Motive::withUserId($request->user()->id)
            ->dispatches()
            ->create($request->validated());

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'dispatch.created',
            'resource_id' => $dispatch->id,
        ]);

        return $dispatch;
    }
}
```

## HTTP Headers

Context modifiers set the following HTTP headers:

| Modifier | Header |
|----------|--------|
| `withTimezone()` | `X-Timezone` |
| `withMetricUnits()` | `X-Metric-Units` |
| `withUserId()` | `X-User-Id` |

These headers are included in every API request made by the configured instance.

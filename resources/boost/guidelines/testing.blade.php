## Motive SDK Testing

The SDK provides comprehensive testing support with fakes, factories, and assertions.

### Using MotiveFake

@verbatim
<code-snippet name="Basic fake setup" lang="php">
use Motive\Facades\Motive;
use Motive\Testing\MotiveFake;

public function test_it_syncs_vehicles(): void
{
    // Replace Motive with fake
    Motive::fake();

    // Your application code
    $this->artisan('sync:vehicles');

    // Assert requests were made
    Motive::assertRequested('vehicles.list');
}
</code-snippet>
@endverbatim

### Configuring Fake Responses

@verbatim
<code-snippet name="Configure fake responses" lang="php">
use Motive\Data\Vehicle;
use Motive\Testing\Factories\VehicleFactory;

// Using factory data
Motive::fake([
    'vehicles' => Vehicle::factory()->count(5)->make(),
]);

// Using array data
Motive::fake([
    'vehicles' => [
        ['id' => 1, 'number' => 'TRUCK-001', 'status' => 'active'],
        ['id' => 2, 'number' => 'TRUCK-002', 'status' => 'active'],
    ],
]);

// With specific responses for different endpoints
Motive::fake([
    'vehicles.list' => Vehicle::factory()->count(3)->make(),
    'vehicles.find' => Vehicle::factory()->make(['id' => 123]),
    'users.list' => User::factory()->count(2)->make(),
]);
</code-snippet>
@endverbatim

### Assertions

@verbatim
<code-snippet name="Available assertions" lang="php">
// Assert a specific endpoint was called
Motive::assertRequested('vehicles.list');
Motive::assertRequested('vehicles.find', ['id' => 123]);

// Assert request count
Motive::assertRequestCount(3);
Motive::assertRequestCount(2, 'vehicles.*');

// Assert no requests were made
Motive::assertNothingRequested();

// Assert specific resource was accessed
Motive::assertRequestedVehicles();
Motive::assertRequestedUsers();

// Assert with callback
Motive::assertRequested('vehicles.create', function ($request) {
    return $request['number'] === 'TRUCK-001';
});
</code-snippet>
@endverbatim

### Factories

The SDK includes factories for all major DTOs.

#### Vehicle Factory

@verbatim
<code-snippet name="Vehicle factory" lang="php">
use Motive\Data\Vehicle;

// Create single vehicle
$vehicle = Vehicle::factory()->make();

// Create multiple vehicles
$vehicles = Vehicle::factory()->count(5)->make();

// With specific attributes
$vehicle = Vehicle::factory()->make([
    'number' => 'TRUCK-001',
    'status' => 'active',
]);

// Using states
$vehicle = Vehicle::factory()
    ->inactive()
    ->make();
</code-snippet>
@endverbatim

#### User Factory

@verbatim
<code-snippet name="User factory" lang="php">
use Motive\Data\User;

$user = User::factory()->make();

// With driver relationship
$user = User::factory()
    ->withDriver()
    ->make();

// Admin user
$user = User::factory()
    ->admin()
    ->make();
</code-snippet>
@endverbatim

#### HOS Factories

@verbatim
<code-snippet name="HOS factories" lang="php">
use Motive\Data\HosLog;
use Motive\Data\HosAvailability;

// HOS log
$log = HosLog::factory()->make();
$log = HosLog::factory()->driving()->make();
$log = HosLog::factory()->offDuty()->make();

// HOS availability
$availability = HosAvailability::factory()->make();
$availability = HosAvailability::factory()->lowHours()->make();
</code-snippet>
@endverbatim

#### Dispatch Factory

@verbatim
<code-snippet name="Dispatch factory" lang="php">
use Motive\Data\Dispatch;

$dispatch = Dispatch::factory()->make();

// With stops
$dispatch = Dispatch::factory()
    ->withStops(3)
    ->make();

// Specific status
$dispatch = Dispatch::factory()
    ->completed()
    ->make();
</code-snippet>
@endverbatim

### Complete Test Example

@verbatim
<code-snippet name="Complete test example" lang="php">
<?php

namespace Tests\Feature;

use Motive\Data\Vehicle;
use Motive\Facades\Motive;
use Tests\TestCase;

class VehicleSyncTest extends TestCase
{
    public function test_it_imports_vehicles_from_motive(): void
    {
        // Arrange: Set up fake with factory data
        Motive::fake([
            'vehicles' => Vehicle::factory()->count(3)->make([
                'status' => 'active',
            ]),
        ]);

        // Act: Run sync command
        $this->artisan('vehicles:sync')
            ->assertSuccessful();

        // Assert: Verify requests and database
        Motive::assertRequested('vehicles.list');
        $this->assertDatabaseCount('vehicles', 3);
    }

    public function test_it_handles_api_errors_gracefully(): void
    {
        // Arrange: Set up fake to return error
        Motive::fake();
        Motive::shouldFailWith('vehicles.list', 429, [
            'error' => 'Rate limit exceeded',
        ]);

        // Act & Assert: Command handles error
        $this->artisan('vehicles:sync')
            ->assertFailed()
            ->expectsOutput('Rate limited, will retry later');
    }

    public function test_it_filters_inactive_vehicles(): void
    {
        // Arrange
        Motive::fake([
            'vehicles' => [
                Vehicle::factory()->make(['status' => 'active']),
                Vehicle::factory()->make(['status' => 'inactive']),
                Vehicle::factory()->make(['status' => 'active']),
            ],
        ]);

        // Act
        $this->artisan('vehicles:sync --active-only');

        // Assert: Only active vehicles imported
        $this->assertDatabaseCount('vehicles', 2);
    }
}
</code-snippet>
@endverbatim

### Testing Webhooks

@verbatim
<code-snippet name="Testing webhooks" lang="php">
<?php

namespace Tests\Feature;

use Motive\Webhooks\WebhookPayload;
use Motive\Webhooks\WebhookSignature;
use Tests\TestCase;

class WebhookControllerTest extends TestCase
{
    public function test_it_verifies_webhook_signature(): void
    {
        $payload = json_encode([
            'event' => 'vehicle.location.updated',
            'data' => ['vehicle_id' => 123],
            'timestamp' => now()->timestamp,
        ]);

        $signature = WebhookSignature::generate($payload, config('motive.webhooks.secret'));

        $response = $this->postJson('/webhooks/motive', json_decode($payload, true), [
            'X-Motive-Signature' => $signature,
        ]);

        $response->assertOk();
    }

    public function test_it_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/webhooks/motive', [
            'event' => 'vehicle.location.updated',
        ], [
            'X-Motive-Signature' => 'invalid-signature',
        ]);

        $response->assertUnauthorized();
    }
}
</code-snippet>
@endverbatim

### Testing OAuth Flow

@verbatim
<code-snippet name="Testing OAuth" lang="php">
<?php

namespace Tests\Feature;

use Motive\Facades\Motive;
use Tests\TestCase;

class OAuthFlowTest extends TestCase
{
    public function test_it_generates_authorization_url(): void
    {
        $url = Motive::oauth()->authorizationUrl(
            scopes: ['vehicles:read', 'users:read'],
            state: 'test-state',
        );

        $this->assertStringContainsString('client_id=', $url);
        $this->assertStringContainsString('state=test-state', $url);
    }

    public function test_it_exchanges_code_for_tokens(): void
    {
        Motive::fake();
        Motive::shouldReturnOAuthTokens([
            'access_token' => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'expires_in' => 3600,
        ]);

        $tokens = Motive::oauth()->exchangeCode('test-code');

        $this->assertEquals('test-access-token', $tokens->accessToken);
        $this->assertEquals('test-refresh-token', $tokens->refreshToken);
    }
}
</code-snippet>
@endverbatim

### Mocking HTTP Directly

For more control, you can use Laravel's HTTP fake directly:

@verbatim
<code-snippet name="Using Laravel HTTP fake" lang="php">
use Illuminate\Support\Facades\Http;

public function test_with_http_fake(): void
{
    Http::fake([
        'api.gomotive.com/v1/vehicles*' => Http::response([
            'vehicles' => [
                ['id' => 1, 'number' => 'TRUCK-001'],
            ],
            'pagination' => ['total' => 1, 'per_page' => 25, 'page_no' => 1],
        ], 200),
    ]);

    $vehicles = Motive::vehicles()->list();

    $this->assertCount(1, iterator_to_array($vehicles));

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/v1/vehicles');
    });
}
</code-snippet>
@endverbatim

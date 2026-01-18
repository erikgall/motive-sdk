<?php

namespace Motive\Tests\Feature;

use Motive\MotiveManager;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use InvalidArgumentException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

/**
 * Feature tests for multi-tenancy support.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MultiTenancyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_creates_new_instance_with_metric_units(): void
    {
        $manager = app(MotiveManager::class);
        $withMetric = $manager->withMetricUnits(true);

        // Should return a new instance
        $this->assertNotSame($manager, $withMetric);
        $this->assertInstanceOf(MotiveManager::class, $withMetric);
    }

    #[Test]
    public function it_creates_new_instance_with_timezone(): void
    {
        $manager = app(MotiveManager::class);
        $withTimezone = $manager->withTimezone('America/New_York');

        // Should return a new instance
        $this->assertNotSame($manager, $withTimezone);
        $this->assertInstanceOf(MotiveManager::class, $withTimezone);
    }

    #[Test]
    public function it_creates_new_instance_with_user_id(): void
    {
        $manager = app(MotiveManager::class);
        $withUserId = $manager->withUserId(123);

        // Should return a new instance
        $this->assertNotSame($manager, $withUserId);
        $this->assertInstanceOf(MotiveManager::class, $withUserId);
    }

    #[Test]
    public function it_maintains_separate_state_per_connection(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => ['id' => 1, 'company_id' => 100, 'number' => 'TRUCK-001'],
            ], 200),
        ]);

        // Get manager with custom API key
        $managerA = Motive::withApiKey('key-a');
        $managerB = Motive::withApiKey('key-b');

        // Each manager should maintain its own API key
        $this->assertNotSame($managerA, $managerB);

        // Make requests to verify they use different keys
        $managerA->vehicles()->find(1);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'key-a');
        });
    }

    #[Test]
    public function it_makes_request_with_custom_api_key(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => ['id' => 1, 'company_id' => 100, 'number' => 'TRUCK-001'],
            ], 200),
        ]);

        Motive::withApiKey('custom-api-key-123')->vehicles()->find(1);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'custom-api-key-123');
        });
    }

    #[Test]
    public function it_returns_current_connection_name(): void
    {
        $manager = app(MotiveManager::class);

        $this->assertEquals('default', $manager->getCurrentConnection());
    }

    #[Test]
    public function it_switches_between_connections(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => ['id' => 1, 'company_id' => 100, 'number' => 'TRUCK-001'],
            ], 200),
        ]);

        // Use tenant A
        Motive::connection('tenant_a')->vehicles()->find(1);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'tenant-a-api-key');
        });

        // Use tenant B
        Motive::connection('tenant_b')->vehicles()->find(1);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'tenant-b-api-key');
        });
    }

    #[Test]
    public function it_throws_exception_for_unknown_connection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Connection [unknown_tenant] not configured.');

        Motive::connection('unknown_tenant');
    }

    #[Test]
    public function it_uses_default_connection(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => ['id' => 1, 'company_id' => 100, 'number' => 'TRUCK-001'],
            ], 200),
        ]);

        Motive::vehicles()->find(1);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'test-api-key');
        });
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // Add multiple connections for multi-tenancy testing
        $app['config']->set('motive.connections.tenant_a', [
            'auth_driver' => 'api_key',
            'api_key'     => 'tenant-a-api-key',
            'base_url'    => 'https://api.gomotive.com',
            'timeout'     => 30,
            'retry'       => [
                'times' => 3,
                'sleep' => 100,
            ],
        ]);

        $app['config']->set('motive.connections.tenant_b', [
            'auth_driver' => 'api_key',
            'api_key'     => 'tenant-b-api-key',
            'base_url'    => 'https://api.gomotive.com',
            'timeout'     => 30,
            'retry'       => [
                'times' => 3,
                'sleep' => 100,
            ],
        ]);
    }
}

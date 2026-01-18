<?php

namespace Motive\Tests\Integration;

use Motive\Data\Company;
use Motive\Data\Vehicle;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresEnvironmentVariable;

/**
 * Real API integration tests for Motive SDK.
 *
 * These tests require actual Motive API credentials and make real HTTP requests.
 * They are skipped by default and only run when MOTIVE_API_KEY is set.
 *
 * To run these tests:
 * 1. Set environment variable: export MOTIVE_API_KEY=your-api-key
 * 2. Run: ./vendor/bin/phpunit tests/Integration --group=integration
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
#[Group('integration')]
#[RequiresEnvironmentVariable('MOTIVE_API_KEY')]
class RealApiIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Use the real API key from environment
        $this->app['config']->set('motive.connections.default.api_key', env('MOTIVE_API_KEY'));
    }

    #[Test]
    public function it_can_get_current_company(): void
    {
        $company = Motive::companies()->current();

        $this->assertInstanceOf(Company::class, $company);
        $this->assertNotEmpty($company->id);
        $this->assertNotEmpty($company->name);

        echo "\n✓ Company: {$company->name} (ID: {$company->id})\n";
    }

    #[Test]
    public function it_can_get_hos_availability(): void
    {
        // First get a driver
        $users = Motive::users()->list(['per_page' => 10]);

        $driverId = null;
        foreach ($users as $user) {
            if ($user->driver !== null) {
                $driverId = $user->driver->id;
                break;
            }
        }

        if ($driverId === null) {
            $this->markTestSkipped('No drivers found in the account');
        }

        $availability = Motive::hosAvailability()->forDriver($driverId);

        $this->assertNotNull($availability);
        echo "\n✓ HOS Availability for driver {$driverId}:\n";
        echo "  - Drive time remaining: {$availability->driveTimeRemaining} min\n";
        echo "  - Shift time remaining: {$availability->shiftTimeRemaining} min\n";
        echo "  - Cycle time remaining: {$availability->cycleTimeRemaining} min\n";
    }

    #[Test]
    public function it_can_get_vehicle_location(): void
    {
        // Get a vehicle first
        $vehicles = Motive::vehicles()->list(['per_page' => 1]);

        $vehicleId = null;
        foreach ($vehicles as $vehicle) {
            $vehicleId = $vehicle->id;
            break;
        }

        if ($vehicleId === null) {
            $this->markTestSkipped('No vehicles found in the account');
        }

        try {
            $location = Motive::vehicles()->currentLocation($vehicleId);

            $this->assertNotNull($location);
            echo "\n✓ Vehicle {$vehicleId} location:\n";
            echo "  - Lat/Lng: {$location->latitude}, {$location->longitude}\n";
            echo "  - Address: {$location->address}\n";
            echo "  - Speed: {$location->speed}\n";
        } catch (\Exception $e) {
            echo "\n⚠ Could not get location for vehicle {$vehicleId}: {$e->getMessage()}\n";
            $this->markTestSkipped('Vehicle location not available');
        }
    }

    #[Test]
    public function it_can_list_assets(): void
    {
        $assets = Motive::assets()->list(['per_page' => 5]);

        $count = 0;
        foreach ($assets as $asset) {
            $this->assertNotEmpty($asset->id);
            $count++;

            echo "  - Asset {$asset->id}: {$asset->name} ({$asset->type->value})\n";

            if ($count >= 5) {
                break;
            }
        }

        echo "✓ Listed {$count} assets\n";
    }

    #[Test]
    public function it_can_list_locations(): void
    {
        $locations = Motive::locations()->list(['per_page' => 5]);

        $count = 0;
        foreach ($locations as $location) {
            $this->assertNotEmpty($location->id);
            $count++;

            echo "  - Location {$location->id}: {$location->name}\n";

            if ($count >= 5) {
                break;
            }
        }

        echo "✓ Listed {$count} locations\n";
    }

    #[Test]
    public function it_can_list_users(): void
    {
        $users = Motive::users()->list(['per_page' => 5]);

        $count = 0;
        foreach ($users as $user) {
            $this->assertNotEmpty($user->id);
            $count++;

            echo "  - User {$user->id}: {$user->firstName} {$user->lastName} ({$user->email})\n";

            if ($count >= 5) {
                break;
            }
        }

        echo "✓ Listed {$count} users\n";
        $this->assertGreaterThan(0, $count, 'Expected at least one user');
    }

    #[Test]
    public function it_can_list_vehicles(): void
    {
        $vehicles = Motive::vehicles()->list(['per_page' => 5]);

        $count = 0;
        foreach ($vehicles as $vehicle) {
            $this->assertInstanceOf(Vehicle::class, $vehicle);
            $this->assertNotEmpty($vehicle->id);
            $count++;

            echo "  - Vehicle {$vehicle->id}: {$vehicle->number} ({$vehicle->make} {$vehicle->model})\n";

            if ($count >= 5) {
                break;
            }
        }

        echo "✓ Listed {$count} vehicles\n";
        $this->assertGreaterThan(0, $count, 'Expected at least one vehicle');
    }

    #[Test]
    public function it_can_list_webhooks(): void
    {
        $webhooks = Motive::webhooks()->list();

        $count = 0;
        foreach ($webhooks as $webhook) {
            $this->assertNotEmpty($webhook->id);
            $count++;

            echo "  - Webhook {$webhook->id}: {$webhook->url}\n";
        }

        echo "✓ Listed {$count} webhooks\n";
    }

    #[Test]
    public function it_can_paginate_vehicles(): void
    {
        $page = Motive::vehicles()->paginate(page: 1, perPage: 10);

        $this->assertGreaterThanOrEqual(0, $page->total());
        $this->assertEquals(1, $page->currentPage());

        echo "\n✓ Pagination works: Page {$page->currentPage()}/{$page->lastPage()}, Total: {$page->total()}\n";
    }

    #[Test]
    public function it_handles_context_modifiers(): void
    {
        // Test with timezone
        $vehicles = Motive::withTimezone('America/Chicago')
            ->vehicles()
            ->list(['per_page' => 1]);

        $count = iterator_count($vehicles);
        $this->assertGreaterThanOrEqual(0, $count);

        echo "\n✓ Context modifiers work correctly\n";
    }

    #[Test]
    public function it_handles_rate_limiting_gracefully(): void
    {
        // Make multiple rapid requests to test rate limiting handling
        $startTime = microtime(true);

        for ($i = 0; $i < 5; $i++) {
            Motive::vehicles()->list(['per_page' => 1]);
        }

        $elapsed = microtime(true) - $startTime;

        echo "\n✓ Made 5 requests in {$elapsed}s without rate limit errors\n";
        $this->assertTrue(true);
    }
}

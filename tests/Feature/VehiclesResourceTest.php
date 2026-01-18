<?php

namespace Motive\Tests\Feature;

use Motive\Data\Vehicle;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use Motive\Enums\VehicleStatus;
use Motive\Data\VehicleLocation;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Vehicles\VehiclesResource;

/**
 * Feature tests for VehiclesResource integration.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehiclesResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_creates_vehicle_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles' => Http::response([
                'vehicle' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'number'     => 'TRUCK-001',
                    'make'       => 'Freightliner',
                    'model'      => 'Cascadia',
                    'year'       => 2024,
                    'vin'        => '1FUJGBDV5PLEF1234',
                    'status'     => 'active',
                    'created_at' => '2024-01-15T10:00:00Z',
                    'updated_at' => '2024-01-15T10:00:00Z',
                ],
            ], 201),
        ]);

        $vehicle = Motive::vehicles()->create([
            'number' => 'TRUCK-001',
            'make'   => 'Freightliner',
            'model'  => 'Cascadia',
            'year'   => 2024,
            'vin'    => '1FUJGBDV5PLEF1234',
        ]);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals(1, $vehicle->id);
        $this->assertEquals('TRUCK-001', $vehicle->number);
        $this->assertEquals('Freightliner', $vehicle->make);
        $this->assertEquals(VehicleStatus::Active, $vehicle->status);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'test-api-key')
                && str_contains($request->url(), '/v1/vehicles')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_deletes_vehicle_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/123' => Http::response([], 204),
        ]);

        $result = Motive::vehicles()->delete(123);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/vehicles/123')
                && $request->method() === 'DELETE';
        });
    }

    #[Test]
    public function it_finds_vehicle_by_id_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'number'     => 'TRUCK-001',
                    'make'       => 'Freightliner',
                    'model'      => 'Cascadia',
                    'year'       => 2024,
                    'status'     => 'active',
                ],
            ], 200),
        ]);

        $vehicle = Motive::vehicles()->find(1);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals(1, $vehicle->id);
        $this->assertEquals('TRUCK-001', $vehicle->number);
    }

    #[Test]
    public function it_finds_vehicle_by_number_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/by_number/TRUCK-001' => Http::response([
                'vehicle' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'number'     => 'TRUCK-001',
                    'make'       => 'Volvo',
                ],
            ], 200),
        ]);

        $vehicle = Motive::vehicles()->findByNumber('TRUCK-001');

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals('TRUCK-001', $vehicle->number);
    }

    #[Test]
    public function it_gets_current_location_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1/current_location' => Http::response([
                'vehicle_location' => [
                    'latitude'   => 37.7749,
                    'longitude'  => -122.4194,
                    'speed'      => 65.5,
                    'bearing'    => 180,
                    'address'    => '123 Market St, San Francisco, CA',
                    'located_at' => '2024-01-15T14:30:00Z',
                ],
            ], 200),
        ]);

        $location = Motive::vehicles()->currentLocation(1);

        $this->assertInstanceOf(VehicleLocation::class, $location);
        $this->assertEquals(37.7749, $location->latitude);
        $this->assertEquals(-122.4194, $location->longitude);
        $this->assertEquals(65.5, $location->speed);
    }

    #[Test]
    public function it_gets_vehicles_resource_from_manager(): void
    {
        $resource = Motive::vehicles();

        $this->assertInstanceOf(VehiclesResource::class, $resource);
    }

    #[Test]
    public function it_lists_vehicles_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles*' => Http::response([
                'vehicles' => [
                    [
                        'id'         => 1,
                        'company_id' => 100,
                        'number'     => 'TRUCK-001',
                        'make'       => 'Freightliner',
                        'status'     => 'active',
                    ],
                    [
                        'id'         => 2,
                        'company_id' => 100,
                        'number'     => 'TRUCK-002',
                        'make'       => 'Volvo',
                        'status'     => 'active',
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 2,
                ],
            ], 200),
        ]);

        $vehicles = Motive::vehicles()->list();

        $this->assertCount(2, iterator_to_array($vehicles));
    }

    #[Test]
    public function it_paginates_vehicles_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles*' => Http::response([
                'vehicles' => [
                    [
                        'id'         => 1,
                        'company_id' => 100,
                        'number'     => 'TRUCK-001',
                    ],
                ],
                'pagination' => [
                    'per_page' => 10,
                    'page_no'  => 1,
                    'total'    => 50,
                ],
            ], 200),
        ]);

        $result = Motive::vehicles()->paginate(1, 10);

        $this->assertCount(1, $result->items());
        $this->assertEquals(50, $result->total());
        $this->assertEquals(10, $result->perPage());
        $this->assertTrue($result->hasMorePages());
    }

    #[Test]
    public function it_updates_vehicle_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'number'     => 'TRUCK-001',
                    'status'     => 'inactive',
                ],
            ], 200),
        ]);

        $vehicle = Motive::vehicles()->update(1, ['status' => 'inactive']);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals(VehicleStatus::Inactive, $vehicle->status);
    }

    #[Test]
    public function it_uses_custom_api_key(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles/1' => Http::response([
                'vehicle' => ['id' => 1, 'company_id' => 100, 'number' => 'TRUCK-001'],
            ], 200),
        ]);

        Motive::withApiKey('custom-api-key')->vehicles()->find(1);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key', 'custom-api-key');
        });
    }
}

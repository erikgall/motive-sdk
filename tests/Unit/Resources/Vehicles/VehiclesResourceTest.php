<?php

namespace Motive\Tests\Unit\Resources\Vehicles;

use Motive\Data\Vehicle;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use Motive\Enums\VehicleStatus;
use PHPUnit\Framework\TestCase;
use Motive\Data\VehicleLocation;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Vehicles\VehiclesResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehiclesResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new VehiclesResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/vehicles', $resource->fullPath());
        $this->assertSame('/v1/vehicles/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_creates_vehicle(): void
    {
        $vehicleData = [
            'id'         => 123,
            'company_id' => 456,
            'number'     => 'V-002',
            'make'       => 'Volvo',
            'model'      => 'VNL',
        ];

        $response = $this->createMockResponse(['vehicle' => $vehicleData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/vehicles', ['vehicle' => ['number' => 'V-002', 'make' => 'Volvo', 'model' => 'VNL']])
            ->willReturn($response);

        $resource = new VehiclesResource($client);
        $vehicle = $resource->create([
            'number' => 'V-002',
            'make'   => 'Volvo',
            'model'  => 'VNL',
        ]);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('V-002', $vehicle->number);
    }

    #[Test]
    public function it_deletes_vehicle(): void
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('status')->willReturn(204);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/vehicles/123')
            ->willReturn($response);

        $resource = new VehiclesResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_vehicle_by_id(): void
    {
        $vehicleData = [
            'id'         => 123,
            'company_id' => 456,
            'number'     => 'V-001',
            'make'       => 'Freightliner',
            'status'     => 'active',
        ];

        $response = $this->createMockResponse(['vehicle' => $vehicleData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/vehicles/123')
            ->willReturn($response);

        $resource = new VehiclesResource($client);
        $vehicle = $resource->find(123);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame(123, $vehicle->id);
        $this->assertSame('V-001', $vehicle->number);
        $this->assertSame(VehicleStatus::Active, $vehicle->status);
    }

    #[Test]
    public function it_finds_vehicle_by_number(): void
    {
        $vehicleData = [
            'id'         => 123,
            'company_id' => 456,
            'number'     => 'V-001',
        ];

        $response = $this->createMockResponse(['vehicle' => $vehicleData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/vehicles/by_number/V-001')
            ->willReturn($response);

        $resource = new VehiclesResource($client);
        $vehicle = $resource->findByNumber('V-001');

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('V-001', $vehicle->number);
    }

    #[Test]
    public function it_gets_current_location(): void
    {
        $locationData = [
            'latitude'   => 37.7749,
            'longitude'  => -122.4194,
            'speed'      => 55.5,
            'bearing'    => 180,
            'address'    => '123 Main St',
            'located_at' => '2024-01-15T10:30:00Z',
        ];

        $response = $this->createMockResponse(['vehicle_location' => $locationData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/vehicles/123/current_location')
            ->willReturn($response);

        $resource = new VehiclesResource($client);
        $location = $resource->currentLocation(123);

        $this->assertInstanceOf(VehicleLocation::class, $location);
        $this->assertSame(37.7749, $location->latitude);
        $this->assertSame(-122.4194, $location->longitude);
        $this->assertSame(55.5, $location->speed);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new VehiclesResource($this->createStub(MotiveClient::class));

        $this->assertSame('vehicles', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new VehiclesResource($this->createStub(MotiveClient::class));

        $this->assertSame('vehicle', $resource->getResourceKey());
    }

    #[Test]
    public function it_updates_vehicle(): void
    {
        $vehicleData = [
            'id'         => 123,
            'company_id' => 456,
            'number'     => 'V-001',
            'make'       => 'Freightliner',
            'status'     => 'inactive',
        ];

        $response = $this->createMockResponse(['vehicle' => $vehicleData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('/v1/vehicles/123', ['vehicle' => ['status' => 'inactive']])
            ->willReturn($response);

        $resource = new VehiclesResource($client);
        $vehicle = $resource->update(123, ['status' => 'inactive']);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame(VehicleStatus::Inactive, $vehicle->status);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}

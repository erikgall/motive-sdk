<?php

namespace Motive\Tests\Unit\Resources\Locations;

use Motive\Data\Location;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Locations\LocationsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class LocationsResourceTest extends TestCase
{
    private MotiveClient $client;

    private LocationsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new LocationsResource($this->client);
    }

    #[Test]
    public function it_finds_location_by_id(): void
    {
        $locationData = [
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Warehouse A',
            'latitude'   => 37.7749,
            'longitude'  => -122.4194,
        ];

        $response = $this->createMockResponse(['location' => $locationData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/locations/123')
            ->willReturn($response);

        $location = $this->resource->find(123);

        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame(123, $location->id);
        $this->assertSame('Warehouse A', $location->name);
    }

    #[Test]
    public function it_finds_nearest_locations(): void
    {
        $locationData = [
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Nearest Location',
            'latitude'   => 37.7750,
            'longitude'  => -122.4195,
        ];

        $response = $this->createMockResponse(['locations' => [$locationData]]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/locations/nearest', [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
                'radius'    => 1000,
            ])
            ->willReturn($response);

        $locations = $this->resource->findNearest(37.7749, -122.4194, 1000);

        $this->assertCount(1, $locations);
        $this->assertInstanceOf(Location::class, $locations->first());
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('locations', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('location', $this->resource->getResourceKey());
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}

<?php

namespace Motive\Tests\Unit\Resources\Geofences;

use Motive\Data\Geofence;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Geofences\GeofencesResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofencesResourceTest extends TestCase
{
    #[Test]
    public function it_creates_geofence(): void
    {
        $geofenceData = [
            'id'            => 123,
            'company_id'    => 456,
            'name'          => 'New Zone',
            'geofence_type' => 'circle',
        ];

        $response = $this->createMockResponse(['geofence' => $geofenceData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/geofences', ['geofence' => ['name' => 'New Zone', 'geofence_type' => 'circle']])
            ->willReturn($response);

        $resource = new GeofencesResource($client);
        $geofence = $resource->create(['name' => 'New Zone', 'geofence_type' => 'circle']);

        $this->assertInstanceOf(Geofence::class, $geofence);
        $this->assertSame('New Zone', $geofence->name);
    }

    #[Test]
    public function it_deletes_geofence(): void
    {
        $response = $this->createMockResponse([], 204);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/geofences/123')
            ->willReturn($response);

        $resource = new GeofencesResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_geofence_by_id(): void
    {
        $geofenceData = [
            'id'            => 123,
            'company_id'    => 456,
            'name'          => 'Zone A',
            'geofence_type' => 'circle',
            'radius'        => 500,
        ];

        $response = $this->createMockResponse(['geofence' => $geofenceData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/geofences/123')
            ->willReturn($response);

        $resource = new GeofencesResource($client);
        $geofence = $resource->find(123);

        $this->assertInstanceOf(Geofence::class, $geofence);
        $this->assertSame(123, $geofence->id);
        $this->assertSame('Zone A', $geofence->name);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new GeofencesResource($this->createStub(MotiveClient::class));

        $this->assertSame('geofences', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new GeofencesResource($this->createStub(MotiveClient::class));

        $this->assertSame('geofence', $resource->getResourceKey());
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

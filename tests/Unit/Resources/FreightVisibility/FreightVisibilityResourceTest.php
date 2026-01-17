<?php

namespace Motive\Tests\Unit\Resources\FreightVisibility;

use Motive\Data\Shipment;
use Motive\Client\Response;
use Motive\Data\ShipmentEta;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Data\ShipmentTracking;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\FreightVisibility\FreightVisibilityResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FreightVisibilityResourceTest extends TestCase
{
    private MotiveClient $client;

    private FreightVisibilityResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new FreightVisibilityResource($this->client);
    }

    #[Test]
    public function it_gets_shipment_eta(): void
    {
        $etaData = [
            'id'                 => 1,
            'shipment_id'        => 123,
            'distance_remaining' => 150.5,
            'time_remaining'     => 7200,
        ];

        $response = $this->createMockResponse(['shipment_eta' => $etaData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/shipments/123/eta')
            ->willReturn($response);

        $eta = $this->resource->eta(123);

        $this->assertInstanceOf(ShipmentEta::class, $eta);
        $this->assertSame(123, $eta->shipmentId);
        $this->assertSame(150.5, $eta->distanceRemaining);
    }

    #[Test]
    public function it_gets_shipment_tracking(): void
    {
        $trackingData = [
            'id'               => 1,
            'shipment_id'      => 123,
            'current_location' => ['lat' => 37.7749, 'lng' => -122.4194],
            'speed'            => 65.5,
        ];

        $response = $this->createMockResponse(['shipment_tracking' => $trackingData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/shipments/123/tracking')
            ->willReturn($response);

        $tracking = $this->resource->tracking(123);

        $this->assertInstanceOf(ShipmentTracking::class, $tracking);
        $this->assertSame(123, $tracking->shipmentId);
        $this->assertSame(65.5, $tracking->speed);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('shipments', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('shipment', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_lists_shipments(): void
    {
        $shipmentsData = [
            [
                'id'     => 1,
                'status' => 'in_transit',
                'origin' => 'Los Angeles, CA',
            ],
            [
                'id'     => 2,
                'status' => 'delivered',
                'origin' => 'San Francisco, CA',
            ],
        ];

        $response = $this->createMockResponse([
            'shipments'  => $shipmentsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $this->client->method('get')->willReturn($response);

        $shipments = $this->resource->shipments();

        $this->assertInstanceOf(LazyCollection::class, $shipments);

        $shipmentsArray = $shipments->all();
        $this->assertCount(2, $shipmentsArray);
        $this->assertInstanceOf(Shipment::class, $shipmentsArray[0]);
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

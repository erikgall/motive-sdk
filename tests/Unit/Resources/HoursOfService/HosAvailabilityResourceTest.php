<?php

namespace Motive\Tests\Unit\Resources\HoursOfService;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use Motive\Data\HosAvailability;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\HoursOfService\HosAvailabilityResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosAvailabilityResourceTest extends TestCase
{
    private MotiveClient $client;

    private HosAvailabilityResource $resource;

    protected function setUp(): void
    {
        $this->client   = $this->createMock(MotiveClient::class);
        $this->resource = new HosAvailabilityResource($this->client);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('hos_availability', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('hos_availability', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_gets_availability_for_driver(): void
    {
        $availabilityData = [
            'driver_id'            => 456,
            'drive_time_remaining' => 28800,
            'shift_time_remaining' => 50400,
            'cycle_time_remaining' => 252000,
        ];

        $response = $this->createMockResponse(['hos_availability' => $availabilityData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/hos_availability/driver/456')
            ->willReturn($response);

        $availability = $this->resource->forDriver(456);

        $this->assertInstanceOf(HosAvailability::class, $availability);
        $this->assertSame(456, $availability->driverId);
        $this->assertSame(28800, $availability->driveTimeRemaining);
        $this->assertSame(50400, $availability->shiftTimeRemaining);
        $this->assertSame(252000, $availability->cycleTimeRemaining);
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

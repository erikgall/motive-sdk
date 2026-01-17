<?php

namespace Motive\Tests\Unit\Resources\HoursOfService;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use Motive\Data\HosViolation;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\HoursOfService\HosViolationsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosViolationsResourceTest extends TestCase
{
    private MotiveClient $client;

    private HosViolationsResource $resource;

    protected function setUp(): void
    {
        $this->client   = $this->createMock(MotiveClient::class);
        $this->resource = new HosViolationsResource($this->client);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('hos_violations', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('hos_violation', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_finds_violation_by_id(): void
    {
        $violationData = [
            'id'             => 123,
            'driver_id'      => 456,
            'violation_type' => 'drive_time',
            'start_time'     => '2024-01-15T08:00:00Z',
            'severity'       => 'critical',
        ];

        $response = $this->createMockResponse(['hos_violation' => $violationData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/hos_violations/123')
            ->willReturn($response);

        $violation = $this->resource->find(123);

        $this->assertInstanceOf(HosViolation::class, $violation);
        $this->assertSame(123, $violation->id);
        $this->assertSame(456, $violation->driverId);
        $this->assertSame('critical', $violation->severity);
    }

    #[Test]
    public function it_gets_violations_for_driver(): void
    {
        $violationData = [
            'id'             => 123,
            'driver_id'      => 456,
            'violation_type' => 'shift_time',
            'start_time'     => '2024-01-15T08:00:00Z',
        ];

        $response = $this->createMockResponse(['hos_violation' => $violationData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/hos_violations/driver/456')
            ->willReturn($response);

        $violations = $this->resource->forDriver(456);

        $this->assertIsArray($violations);
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

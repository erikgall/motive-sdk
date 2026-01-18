<?php

namespace Motive\Tests\Unit\Resources\HoursOfService;

use Motive\Client\Response;
use Motive\Data\HosViolation;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\HoursOfService\HosViolationsResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosViolationsResourceTest extends TestCase
{
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

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/hos_violations/123')
            ->willReturn($response);

        $resource = new HosViolationsResource($client);
        $violation = $resource->find(123);

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

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/hos_violations/driver/456')
            ->willReturn($response);

        $resource = new HosViolationsResource($client);
        $violations = $resource->forDriver(456);

        $this->assertIsArray($violations);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new HosViolationsResource($this->createStub(MotiveClient::class));

        $this->assertSame('hos_violations', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new HosViolationsResource($this->createStub(MotiveClient::class));

        $this->assertSame('hos_violation', $resource->getResourceKey());
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

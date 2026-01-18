<?php

namespace Motive\Tests\Unit\Resources\HoursOfService;

use Motive\Data\HosLog;
use Motive\Client\Response;
use Motive\Enums\DutyStatus;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\HoursOfService\HosLogsResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosLogsResourceTest extends TestCase
{
    private MotiveClient $client;

    private HosLogsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new HosLogsResource($this->client);
    }

    #[Test]
    public function it_certifies_driver_logs(): void
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('status')->willReturn(200);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/hos_logs/certify', [
                'driver_id' => 456,
                'date'      => '2024-01-15',
            ])
            ->willReturn($response);

        $result = $this->resource->certify(456, '2024-01-15');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_creates_hos_log(): void
    {
        $hosLogData = [
            'id'          => 123,
            'driver_id'   => 456,
            'duty_status' => 'on_duty',
            'start_time'  => '2024-01-15T08:00:00Z',
        ];

        $response = $this->createMockResponse(['hos_log' => $hosLogData], 201);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/hos_logs', ['hos_log' => [
                'driver_id'   => 456,
                'duty_status' => 'on_duty',
                'start_time'  => '2024-01-15T08:00:00Z',
            ]])
            ->willReturn($response);

        $hosLog = $this->resource->create([
            'driver_id'   => 456,
            'duty_status' => 'on_duty',
            'start_time'  => '2024-01-15T08:00:00Z',
        ]);

        $this->assertInstanceOf(HosLog::class, $hosLog);
        $this->assertSame(DutyStatus::OnDuty, $hosLog->dutyStatus);
    }

    #[Test]
    public function it_deletes_hos_log(): void
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('status')->willReturn(204);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $this->client->expects($this->once())
            ->method('delete')
            ->with('/v1/hos_logs/123')
            ->willReturn($response);

        $result = $this->resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_hos_log_by_id(): void
    {
        $hosLogData = [
            'id'          => 123,
            'driver_id'   => 456,
            'duty_status' => 'driving',
            'start_time'  => '2024-01-15T08:00:00Z',
            'duration'    => 14400,
        ];

        $response = $this->createMockResponse(['hos_log' => $hosLogData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/hos_logs/123')
            ->willReturn($response);

        $hosLog = $this->resource->find(123);

        $this->assertInstanceOf(HosLog::class, $hosLog);
        $this->assertSame(123, $hosLog->id);
        $this->assertSame(456, $hosLog->driverId);
        $this->assertSame(DutyStatus::Driving, $hosLog->dutyStatus);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('hos_logs', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('hos_log', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_updates_hos_log(): void
    {
        $hosLogData = [
            'id'          => 123,
            'driver_id'   => 456,
            'duty_status' => 'off_duty',
            'start_time'  => '2024-01-15T08:00:00Z',
            'annotation'  => 'End of shift',
        ];

        $response = $this->createMockResponse(['hos_log' => $hosLogData]);

        $this->client->expects($this->once())
            ->method('patch')
            ->with('/v1/hos_logs/123', ['hos_log' => ['annotation' => 'End of shift']])
            ->willReturn($response);

        $hosLog = $this->resource->update(123, ['annotation' => 'End of shift']);

        $this->assertInstanceOf(HosLog::class, $hosLog);
        $this->assertSame('End of shift', $hosLog->annotation);
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

<?php

namespace Motive\Tests\Unit\Resources\Inspections;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Data\InspectionReport;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\Inspections\InspectionReportsResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionReportsResourceTest extends TestCase
{
    private MotiveClient $client;

    private InspectionReportsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new InspectionReportsResource($this->client);
    }

    #[Test]
    public function it_finds_report_by_id(): void
    {
        $reportData = [
            'id'              => 123,
            'driver_id'       => 456,
            'vehicle_id'      => 789,
            'inspection_type' => 'pre_trip',
            'status'          => 'passed',
            'started_at'      => '2024-01-15T08:00:00Z',
        ];

        $response = $this->createMockResponse(['inspection_report' => $reportData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/inspection_reports/123')
            ->willReturn($response);

        $report = $this->resource->find(123);

        $this->assertInstanceOf(InspectionReport::class, $report);
        $this->assertSame(123, $report->id);
        $this->assertSame(456, $report->driverId);
        $this->assertSame(789, $report->vehicleId);
    }

    #[Test]
    public function it_gets_reports_for_driver(): void
    {
        $reportData = [
            'id'              => 123,
            'driver_id'       => 456,
            'inspection_type' => 'pre_trip',
            'started_at'      => '2024-01-15T08:00:00Z',
        ];

        $response = $this->createMockResponse(['inspection_reports' => [$reportData]]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/inspection_reports/driver/456', [])
            ->willReturn($response);

        $reports = $this->resource->forDriver(456);

        $this->assertIsArray($reports);
        $this->assertCount(1, $reports);
        $this->assertInstanceOf(InspectionReport::class, $reports[0]);
    }

    #[Test]
    public function it_gets_reports_for_vehicle(): void
    {
        $reportData = [
            'id'              => 123,
            'driver_id'       => 456,
            'vehicle_id'      => 789,
            'inspection_type' => 'post_trip',
            'started_at'      => '2024-01-15T16:00:00Z',
        ];

        $response = $this->createMockResponse(['inspection_reports' => [$reportData]]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/inspection_reports/vehicle/789', [])
            ->willReturn($response);

        $reports = $this->resource->forVehicle(789);

        $this->assertIsArray($reports);
        $this->assertCount(1, $reports);
        $this->assertInstanceOf(InspectionReport::class, $reports[0]);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('inspection_reports', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('inspection_report', $this->resource->getResourceKey());
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

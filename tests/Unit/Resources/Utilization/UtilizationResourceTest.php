<?php

namespace Motive\Tests\Unit\Resources\Utilization;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use Motive\Data\UtilizationDay;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use Motive\Data\UtilizationReport;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\Utilization\UtilizationResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UtilizationResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new UtilizationResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/utilization', $resource->fullPath());
    }

    #[Test]
    public function it_gets_daily_utilization(): void
    {
        $dailyData = [
            ['date' => '2024-01-01', 'total_miles' => 450.0],
            ['date' => '2024-01-02', 'total_miles' => 475.0],
        ];

        $response = $this->createMockResponse(['daily_utilization' => $dailyData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/utilization/daily', ['start_date' => '2024-01-01', 'end_date' => '2024-01-02', 'vehicle_id' => 789])
            ->willReturn($response);

        $resource = new UtilizationResource($client);
        $daily = $resource->daily([
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-02',
            'vehicle_id' => 789,
        ]);

        $this->assertInstanceOf(Collection::class, $daily);
        $this->assertCount(2, $daily);
        $this->assertInstanceOf(UtilizationDay::class, $daily->first());
        $this->assertSame('2024-01-01', $daily->first()->date);
    }

    #[Test]
    public function it_gets_summary(): void
    {
        $reportData = [
            'id'                     => 125,
            'company_id'             => 456,
            'total_miles'            => 250000.0,
            'utilization_percentage' => 82.5,
        ];

        $response = $this->createMockResponse(['utilization_report' => $reportData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/utilization/summary', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31'])
            ->willReturn($response);

        $resource = new UtilizationResource($client);
        $report = $resource->summary([
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-31',
        ]);

        $this->assertInstanceOf(UtilizationReport::class, $report);
        $this->assertSame(82.5, $report->utilizationPercentage);
    }

    #[Test]
    public function it_gets_utilization_for_fleet(): void
    {
        $reportData = [
            'id'          => 124,
            'company_id'  => 456,
            'total_miles' => 150000.5,
        ];

        $response = $this->createMockResponse(['utilization_report' => $reportData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/utilization/fleet', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31'])
            ->willReturn($response);

        $resource = new UtilizationResource($client);
        $report = $resource->forFleet([
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-31',
        ]);

        $this->assertInstanceOf(UtilizationReport::class, $report);
        $this->assertSame(150000.5, $report->totalMiles);
    }

    #[Test]
    public function it_gets_utilization_for_vehicle(): void
    {
        $reportData = [
            'id'          => 123,
            'company_id'  => 456,
            'vehicle_id'  => 789,
            'total_miles' => 5500.75,
        ];

        $response = $this->createMockResponse(['utilization_report' => $reportData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/utilization/vehicles/789', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31'])
            ->willReturn($response);

        $resource = new UtilizationResource($client);
        $report = $resource->forVehicle(789, [
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-31',
        ]);

        $this->assertInstanceOf(UtilizationReport::class, $report);
        $this->assertSame(789, $report->vehicleId);
        $this->assertSame(5500.75, $report->totalMiles);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new UtilizationResource($this->createStub(MotiveClient::class));

        $this->assertSame('utilization', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new UtilizationResource($this->createStub(MotiveClient::class));

        $this->assertSame('utilization_report', $resource->getResourceKey());
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

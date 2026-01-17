<?php

namespace Motive\Tests\Unit\Resources\DrivingPeriods;

use Motive\Client\Response;
use Motive\Data\DrivingPeriod;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\DrivingPeriods\DrivingPeriodsResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DrivingPeriodsResourceTest extends TestCase
{
    private MotiveClient $client;

    private DrivingPeriodsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new DrivingPeriodsResource($this->client);
    }

    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $this->assertSame('/v1/driving_periods', $this->resource->fullPath());
        $this->assertSame('/v1/driving_periods/123', $this->resource->fullPath('123'));
    }

    #[Test]
    public function it_finds_a_driving_period(): void
    {
        $periodData = [
            'id'         => 123,
            'driver_id'  => 100,
            'vehicle_id' => 200,
            'start_time' => '2024-01-15T08:00:00Z',
            'distance'   => 150.5,
        ];

        $response = $this->createMockResponse(['driving_period' => $periodData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driving_periods/123')
            ->willReturn($response);

        $period = $this->resource->find(123);

        $this->assertInstanceOf(DrivingPeriod::class, $period);
        $this->assertSame(123, $period->id);
        $this->assertSame(150.5, $period->distance);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('driving_periods', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('driving_period', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_lists_driving_periods(): void
    {
        $periodsData = [
            [
                'id'         => 1,
                'driver_id'  => 100,
                'vehicle_id' => 200,
                'start_time' => '2024-01-15T08:00:00Z',
                'end_time'   => '2024-01-15T16:00:00Z',
            ],
            [
                'id'         => 2,
                'driver_id'  => 101,
                'vehicle_id' => 201,
                'start_time' => '2024-01-15T09:00:00Z',
                'end_time'   => '2024-01-15T17:00:00Z',
            ],
        ];

        $response = $this->createMockResponse([
            'driving_periods' => $periodsData,
            'pagination'      => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $this->client->method('get')->willReturn($response);

        $periods = $this->resource->list();

        $this->assertInstanceOf(LazyCollection::class, $periods);

        $periodsArray = $periods->all();
        $this->assertCount(2, $periodsArray);
        $this->assertInstanceOf(DrivingPeriod::class, $periodsArray[0]);
    }

    #[Test]
    public function it_lists_periods_for_date_range(): void
    {
        $periodsData = [
            [
                'id'         => 1,
                'driver_id'  => 100,
                'start_time' => '2024-01-15T08:00:00Z',
            ],
        ];

        $response = $this->createMockResponse([
            'driving_periods' => $periodsData,
            'pagination'      => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driving_periods', [
                'start_date' => '2024-01-01',
                'end_date'   => '2024-01-31',
                'page_no'    => 1,
                'per_page'   => 25,
            ])
            ->willReturn($response);

        $periods = $this->resource->forDateRange('2024-01-01', '2024-01-31');

        $this->assertInstanceOf(LazyCollection::class, $periods);
        $periodsArray = $periods->all();
        $this->assertCount(1, $periodsArray);
    }

    #[Test]
    public function it_lists_periods_for_driver(): void
    {
        $periodsData = [
            [
                'id'         => 1,
                'driver_id'  => 100,
                'start_time' => '2024-01-15T08:00:00Z',
            ],
        ];

        $response = $this->createMockResponse([
            'driving_periods' => $periodsData,
            'pagination'      => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driving_periods', ['driver_id' => 100, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $periods = $this->resource->forDriver(100);

        $this->assertInstanceOf(LazyCollection::class, $periods);
        $periodsArray = $periods->all();
        $this->assertCount(1, $periodsArray);
        $this->assertSame(100, $periodsArray[0]->driverId);
    }

    #[Test]
    public function it_lists_periods_for_vehicle(): void
    {
        $periodsData = [
            [
                'id'         => 1,
                'driver_id'  => 100,
                'vehicle_id' => 200,
                'start_time' => '2024-01-15T08:00:00Z',
            ],
        ];

        $response = $this->createMockResponse([
            'driving_periods' => $periodsData,
            'pagination'      => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driving_periods', ['vehicle_id' => 200, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $periods = $this->resource->forVehicle(200);

        $this->assertInstanceOf(LazyCollection::class, $periods);
        $periodsArray = $periods->all();
        $this->assertCount(1, $periodsArray);
        $this->assertSame(200, $periodsArray[0]->vehicleId);
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

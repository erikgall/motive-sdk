<?php

namespace Motive\Tests\Unit\Resources\DriverPerformance;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Data\DriverPerformanceEvent;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\DriverPerformance\DriverPerformanceEventsResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DriverPerformanceEventsResourceTest extends TestCase
{
    private MotiveClient $client;

    private DriverPerformanceEventsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new DriverPerformanceEventsResource($this->client);
    }

    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $this->assertSame('/v1/driver_performance_events', $this->resource->fullPath());
        $this->assertSame('/v1/driver_performance_events/123', $this->resource->fullPath('123'));
    }

    #[Test]
    public function it_finds_event_by_id(): void
    {
        $eventData = [
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'event_type' => 'hard_braking',
            'severity'   => 'high',
            'speed'      => 65.5,
            'address'    => '123 Main St',
        ];

        $response = $this->createMockResponse(['driver_performance_event' => $eventData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driver_performance_events/123')
            ->willReturn($response);

        $event = $this->resource->find(123);

        $this->assertInstanceOf(DriverPerformanceEvent::class, $event);
        $this->assertSame(123, $event->id);
        $this->assertSame(65.5, $event->speed);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('driver_performance_events', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('driver_performance_event', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_lists_events(): void
    {
        $eventsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'driver_id'  => 789,
                'event_type' => 'hard_braking',
                'severity'   => 'high',
            ],
            [
                'id'         => 124,
                'company_id' => 456,
                'driver_id'  => 789,
                'event_type' => 'speeding',
                'severity'   => 'medium',
            ],
        ];

        $response = $this->createMockResponse([
            'driver_performance_events' => $eventsData,
            'pagination'                => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $this->client->method('get')->willReturn($response);

        $events = $this->resource->list();

        $this->assertInstanceOf(LazyCollection::class, $events);

        $eventsArray = $events->all();
        $this->assertCount(2, $eventsArray);
        $this->assertInstanceOf(DriverPerformanceEvent::class, $eventsArray[0]);
        $this->assertSame(123, $eventsArray[0]->id);
    }

    #[Test]
    public function it_lists_events_for_date_range(): void
    {
        $eventsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'driver_id'  => 789,
                'event_type' => 'speeding',
                'severity'   => 'low',
            ],
        ];

        $response = $this->createMockResponse([
            'driver_performance_events' => $eventsData,
            'pagination'                => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driver_performance_events', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31', 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $events = $this->resource->forDateRange('2024-01-01', '2024-01-31');

        $this->assertInstanceOf(LazyCollection::class, $events);
        $eventsArray = $events->all();
        $this->assertCount(1, $eventsArray);
    }

    #[Test]
    public function it_lists_events_for_driver(): void
    {
        $eventsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'driver_id'  => 789,
                'event_type' => 'hard_braking',
                'severity'   => 'high',
            ],
        ];

        $response = $this->createMockResponse([
            'driver_performance_events' => $eventsData,
            'pagination'                => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/driver_performance_events', ['driver_id' => 789, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $events = $this->resource->forDriver(789);

        $this->assertInstanceOf(LazyCollection::class, $events);
        $eventsArray = $events->all();
        $this->assertCount(1, $eventsArray);
        $this->assertSame(789, $eventsArray[0]->driverId);
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

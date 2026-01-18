<?php

namespace Motive\Tests\Unit\Resources\Timecards;

use Motive\Data\Timecard;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Timecards\TimecardsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class TimecardsResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new TimecardsResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/timecards', $resource->fullPath());
        $this->assertSame('/v1/timecards/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_finds_timecard_by_id(): void
    {
        $timecardData = [
            'id'          => 123,
            'company_id'  => 456,
            'driver_id'   => 789,
            'date'        => '2024-01-15',
            'status'      => 'approved',
            'total_hours' => 8.5,
        ];

        $response = $this->createMockResponse(['timecard' => $timecardData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/timecards/123')
            ->willReturn($response);

        $resource = new TimecardsResource($client);
        $timecard = $resource->find(123);

        $this->assertInstanceOf(Timecard::class, $timecard);
        $this->assertSame(123, $timecard->id);
        $this->assertSame(8.5, $timecard->totalHours);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new TimecardsResource($this->createStub(MotiveClient::class));

        $this->assertSame('timecards', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new TimecardsResource($this->createStub(MotiveClient::class));

        $this->assertSame('timecard', $resource->getResourceKey());
    }

    #[Test]
    public function it_lists_timecards(): void
    {
        $timecardsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'driver_id'  => 789,
                'date'       => '2024-01-15',
                'status'     => 'approved',
            ],
            [
                'id'         => 124,
                'company_id' => 456,
                'driver_id'  => 790,
                'date'       => '2024-01-15',
                'status'     => 'pending',
            ],
        ];

        $response = $this->createMockResponse([
            'timecards'  => $timecardsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $client = $this->createStub(MotiveClient::class);
        $client->method('get')->willReturn($response);

        $resource = new TimecardsResource($client);
        $timecards = $resource->list();

        $this->assertInstanceOf(LazyCollection::class, $timecards);

        $timecardsArray = $timecards->all();
        $this->assertCount(2, $timecardsArray);
        $this->assertInstanceOf(Timecard::class, $timecardsArray[0]);
        $this->assertSame(123, $timecardsArray[0]->id);
    }

    #[Test]
    public function it_lists_timecards_for_driver(): void
    {
        $timecardsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'driver_id'  => 789,
                'date'       => '2024-01-15',
                'status'     => 'approved',
            ],
        ];

        $response = $this->createMockResponse([
            'timecards'  => $timecardsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/timecards', ['driver_id' => 789, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new TimecardsResource($client);
        $timecards = $resource->forDriver(789);

        $this->assertInstanceOf(LazyCollection::class, $timecards);
        $timecardsArray = $timecards->all();
        $this->assertCount(1, $timecardsArray);
        $this->assertSame(789, $timecardsArray[0]->driverId);
    }

    #[Test]
    public function it_updates_timecard(): void
    {
        $timecardData = [
            'id'          => 123,
            'company_id'  => 456,
            'driver_id'   => 789,
            'date'        => '2024-01-15',
            'status'      => 'approved',
            'total_hours' => 9.0,
        ];

        $response = $this->createMockResponse(['timecard' => $timecardData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('/v1/timecards/123', ['timecard' => ['total_hours' => 9.0]])
            ->willReturn($response);

        $resource = new TimecardsResource($client);
        $timecard = $resource->update(123, ['total_hours' => 9.0]);

        $this->assertInstanceOf(Timecard::class, $timecard);
        $this->assertSame(9.0, $timecard->totalHours);
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

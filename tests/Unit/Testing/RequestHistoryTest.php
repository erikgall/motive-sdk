<?php

namespace Motive\Tests\Unit\Testing;

use PHPUnit\Framework\TestCase;
use Motive\Testing\RequestHistory;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class RequestHistoryTest extends TestCase
{
    #[Test]
    public function it_can_check_if_empty(): void
    {
        $history = new RequestHistory;

        $this->assertTrue($history->isEmpty());

        $history->record('GET', '/v1/vehicles', [], []);

        $this->assertFalse($history->isEmpty());
    }

    #[Test]
    public function it_can_check_if_path_was_requested(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);

        $this->assertTrue($history->hasSent('/v1/vehicles'));
        $this->assertFalse($history->hasSent('/v1/users'));
    }

    #[Test]
    public function it_can_clear_history(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/vehicles', [], []);

        $this->assertCount(2, $history->all());

        $history->clear();

        $this->assertCount(0, $history->all());
        $this->assertTrue($history->isEmpty());
    }

    #[Test]
    public function it_can_count_requests(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/vehicles', [], []);
        $history->record('DELETE', '/v1/vehicles/123', [], []);

        $this->assertSame(3, $history->count());
    }

    #[Test]
    public function it_can_filter_by_method(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/vehicles', [], []);
        $history->record('GET', '/v1/users', [], []);

        $getRequests = $history->forMethod('GET');

        $this->assertCount(2, $getRequests);
    }

    #[Test]
    public function it_can_filter_by_path(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('GET', '/v1/users', [], []);
        $history->record('POST', '/v1/vehicles', [], []);

        $vehicleRequests = $history->forPath('/v1/vehicles');

        $this->assertCount(2, $vehicleRequests);
    }

    #[Test]
    public function it_can_filter_by_path_and_method(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/vehicles', [], []);
        $history->record('GET', '/v1/users', [], []);

        $filtered = $history->filter(
            fn (array $request) => $request['method'] === 'GET' && $request['path'] === '/v1/vehicles'
        );

        $this->assertCount(1, $filtered);
    }

    #[Test]
    public function it_can_get_first_request(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/users', [], []);

        $first = $history->first();

        $this->assertSame('GET', $first['method']);
        $this->assertSame('/v1/vehicles', $first['path']);
    }

    #[Test]
    public function it_can_get_last_request(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/users', [], []);

        $last = $history->last();

        $this->assertSame('POST', $last['method']);
        $this->assertSame('/v1/users', $last['path']);
    }

    #[Test]
    public function it_can_match_path_with_wildcard(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles/123', [], []);
        $history->record('GET', '/v1/vehicles/456/location', [], []);

        $this->assertTrue($history->hasSent('/v1/vehicles/*'));
        $this->assertTrue($history->hasSent('/v1/vehicles/*/location'));
        $this->assertFalse($history->hasSent('/v1/users/*'));
    }

    #[Test]
    public function it_can_record_multiple_requests(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', [], []);
        $history->record('POST', '/v1/vehicles', [], ['vehicle' => ['number' => 'V-001']]);
        $history->record('DELETE', '/v1/vehicles/123', [], []);

        $this->assertCount(3, $history->all());
    }

    #[Test]
    public function it_can_record_request(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', ['page' => 1], []);

        $this->assertCount(1, $history->all());
    }

    #[Test]
    public function it_returns_null_for_first_when_empty(): void
    {
        $history = new RequestHistory;

        $this->assertNull($history->first());
    }

    #[Test]
    public function it_returns_null_for_last_when_empty(): void
    {
        $history = new RequestHistory;

        $this->assertNull($history->last());
    }

    #[Test]
    public function it_stores_request_data(): void
    {
        $history = new RequestHistory;

        $history->record('POST', '/v1/vehicles', [], ['vehicle' => ['number' => 'V-001']]);

        $this->assertSame(['vehicle' => ['number' => 'V-001']], $history->all()[0]['data']);
    }

    #[Test]
    public function it_stores_request_method(): void
    {
        $history = new RequestHistory;

        $history->record('POST', '/v1/vehicles', [], []);

        $this->assertSame('POST', $history->all()[0]['method']);
    }

    #[Test]
    public function it_stores_request_path(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles/123', [], []);

        $this->assertSame('/v1/vehicles/123', $history->all()[0]['path']);
    }

    #[Test]
    public function it_stores_request_query(): void
    {
        $history = new RequestHistory;

        $history->record('GET', '/v1/vehicles', ['driver_id' => 123, 'status' => 'active'], []);

        $this->assertSame(['driver_id' => 123, 'status' => 'active'], $history->all()[0]['query']);
    }

    #[Test]
    public function it_stores_timestamp(): void
    {
        $history = new RequestHistory;

        $beforeTime = microtime(true);
        $history->record('GET', '/v1/vehicles', [], []);
        $afterTime = microtime(true);

        $request = $history->first();

        $this->assertArrayHasKey('timestamp', $request);
        $this->assertGreaterThanOrEqual($beforeTime, $request['timestamp']);
        $this->assertLessThanOrEqual($afterTime, $request['timestamp']);
    }
}

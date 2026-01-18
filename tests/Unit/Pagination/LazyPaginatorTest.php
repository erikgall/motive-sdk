<?php

namespace Motive\Tests\Unit\Pagination;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class LazyPaginatorTest extends TestCase
{
    #[Test]
    public function it_handles_empty_results(): void
    {
        $client = $this->createStub(MotiveClient::class);
        $response = $this->createStub(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'vehicles'   => [],
                'pagination' => [
                    'total'        => 0,
                    'per_page'     => 25,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->method('get')->willReturn($response);

        $paginator = new LazyPaginator($client, '/v1/vehicles', 'vehicles');
        $items = $paginator->cursor()->all();

        $this->assertCount(0, $items);
    }

    #[Test]
    public function it_iterates_through_all_pages(): void
    {
        $client = $this->createMock(MotiveClient::class);

        $page1Response = $this->createStub(Response::class);
        $page1Response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'vehicles' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
                'pagination' => [
                    'total'        => 4,
                    'per_page'     => 2,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $page2Response = $this->createStub(Response::class);
        $page2Response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'vehicles' => [
                    ['id' => 3],
                    ['id' => 4],
                ],
                'pagination' => [
                    'total'        => 4,
                    'per_page'     => 2,
                    'current_page' => 2,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($page1Response, $page2Response);

        $paginator = new LazyPaginator($client, '/v1/vehicles', 'vehicles', 2);
        $items = $paginator->cursor()->all();

        $this->assertCount(4, $items);
        $this->assertEquals(1, $items[0]['id']);
        $this->assertEquals(4, $items[3]['id']);
    }

    #[Test]
    public function it_returns_lazy_collection(): void
    {
        $client = $this->createStub(MotiveClient::class);
        $response = $this->createStub(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'vehicles' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
                'pagination' => [
                    'total'        => 2,
                    'per_page'     => 25,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->method('get')->willReturn($response);

        $paginator = new LazyPaginator($client, '/v1/vehicles', 'vehicles');
        $result = $paginator->cursor();

        $this->assertInstanceOf(LazyCollection::class, $result);
    }

    #[Test]
    public function it_stops_when_no_more_pages(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createStub(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'vehicles' => [
                    ['id' => 1],
                ],
                'pagination' => [
                    'total'        => 1,
                    'per_page'     => 25,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $paginator = new LazyPaginator($client, '/v1/vehicles', 'vehicles');
        $items = $paginator->cursor()->all();

        $this->assertCount(1, $items);
    }
}

<?php

namespace Motive\Tests\Unit\Pagination;

use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Pagination\Paginator;
use PHPUnit\Framework\Attributes\Test;
use Motive\Pagination\PaginatedResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class PaginatorTest extends TestCase
{
    #[Test]
    public function it_fetches_a_single_page(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createStub(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'vehicles' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
                'pagination' => [
                    'total'        => 50,
                    'per_page'     => 25,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->expects($this->once())
            ->method('get')
            ->with('/v1/vehicles', ['page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $paginator = new Paginator($client, '/v1/vehicles', 'vehicles');
        $result = $paginator->paginate(1, 25);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertEquals(50, $result->total());
        $this->assertEquals(1, $result->currentPage());
        $this->assertCount(2, $result->items());
    }

    #[Test]
    public function it_handles_empty_response(): void
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

        $paginator = new Paginator($client, '/v1/vehicles', 'vehicles');
        $result = $paginator->paginate(1, 25);

        $this->assertEquals(0, $result->total());
        $this->assertCount(0, $result->items());
    }

    #[Test]
    public function it_passes_additional_parameters(): void
    {
        $client = $this->createMock(MotiveClient::class);
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

        $client->expects($this->once())
            ->method('get')
            ->with('/v1/vehicles', ['page_no' => 1, 'per_page' => 25, 'status' => 'active'])
            ->willReturn($response);

        $paginator = new Paginator($client, '/v1/vehicles', 'vehicles');
        $paginator->paginate(1, 25, ['status' => 'active']);
    }
}

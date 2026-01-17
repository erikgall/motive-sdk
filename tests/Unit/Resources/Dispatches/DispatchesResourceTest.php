<?php

namespace Motive\Tests\Unit\Resources\Dispatches;

use Motive\Data\Dispatch;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Dispatches\DispatchesResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchesResourceTest extends TestCase
{
    private MotiveClient $client;

    private DispatchesResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new DispatchesResource($this->client);
    }

    #[Test]
    public function it_creates_dispatch(): void
    {
        $dispatchData = [
            'id'         => 123,
            'company_id' => 456,
            'status'     => 'pending',
        ];

        $response = $this->createMockResponse(['dispatch' => $dispatchData], 201);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/dispatches', ['dispatch' => ['company_id' => 456]])
            ->willReturn($response);

        $dispatch = $this->resource->create(['company_id' => 456]);

        $this->assertInstanceOf(Dispatch::class, $dispatch);
        $this->assertSame(123, $dispatch->id);
    }

    #[Test]
    public function it_deletes_dispatch(): void
    {
        $response = $this->createMockResponse([], 204);

        $this->client->expects($this->once())
            ->method('delete')
            ->with('/v1/dispatches/123')
            ->willReturn($response);

        $result = $this->resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_filters_dispatches_by_status(): void
    {
        $dispatchData = [
            'id'         => 123,
            'company_id' => 456,
            'status'     => 'pending',
        ];

        $response = $this->createMockResponse(['dispatches' => [$dispatchData]]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/dispatches/status/pending', [])
            ->willReturn($response);

        $dispatches = $this->resource->byStatus('pending');

        $this->assertIsArray($dispatches);
        $this->assertCount(1, $dispatches);
        $this->assertInstanceOf(Dispatch::class, $dispatches[0]);
    }

    #[Test]
    public function it_finds_dispatch_by_id(): void
    {
        $dispatchData = [
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'status'     => 'in_progress',
        ];

        $response = $this->createMockResponse(['dispatch' => $dispatchData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/dispatches/123')
            ->willReturn($response);

        $dispatch = $this->resource->find(123);

        $this->assertInstanceOf(Dispatch::class, $dispatch);
        $this->assertSame(123, $dispatch->id);
        $this->assertSame(456, $dispatch->companyId);
        $this->assertSame(789, $dispatch->driverId);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('dispatches', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('dispatch', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_updates_dispatch(): void
    {
        $dispatchData = [
            'id'         => 123,
            'company_id' => 456,
            'status'     => 'completed',
        ];

        $response = $this->createMockResponse(['dispatch' => $dispatchData]);

        $this->client->expects($this->once())
            ->method('patch')
            ->with('/v1/dispatches/123', ['dispatch' => ['status' => 'completed']])
            ->willReturn($response);

        $dispatch = $this->resource->update(123, ['status' => 'completed']);

        $this->assertInstanceOf(Dispatch::class, $dispatch);
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

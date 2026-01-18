<?php

namespace Motive\Tests\Unit\Resources\Webhooks;

use Motive\Data\Webhook;
use Motive\Client\Response;
use Motive\Data\WebhookLog;
use Motive\Client\MotiveClient;
use Motive\Enums\WebhookStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Webhooks\WebhooksResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhooksResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new WebhooksResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v2/webhooks', $resource->fullPath());
        $this->assertSame('/v2/webhooks/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_creates_webhook(): void
    {
        $webhookData = [
            'id'         => 789,
            'company_id' => 456,
            'url'        => 'https://example.com/webhooks',
            'events'     => ['vehicle.updated'],
            'status'     => 'active',
        ];

        $response = $this->createMockResponse(['webhook' => $webhookData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v2/webhooks', ['webhook' => ['url' => 'https://example.com/webhooks', 'events' => ['vehicle.updated']]])
            ->willReturn($response);

        $resource = new WebhooksResource($client);
        $webhook = $resource->create([
            'url'    => 'https://example.com/webhooks',
            'events' => ['vehicle.updated'],
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('https://example.com/webhooks', $webhook->url);
    }

    #[Test]
    public function it_deletes_webhook(): void
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('status')->willReturn(204);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v2/webhooks/123')
            ->willReturn($response);

        $resource = new WebhooksResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_webhook_by_id(): void
    {
        $webhookData = [
            'id'         => 123,
            'company_id' => 456,
            'url'        => 'https://example.com/webhooks',
            'events'     => ['vehicle.location_updated'],
            'status'     => 'active',
        ];

        $response = $this->createMockResponse(['webhook' => $webhookData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v2/webhooks/123')
            ->willReturn($response);

        $resource = new WebhooksResource($client);
        $webhook = $resource->find(123);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame(123, $webhook->id);
        $this->assertSame('https://example.com/webhooks', $webhook->url);
        $this->assertSame(WebhookStatus::Active, $webhook->status);
    }

    #[Test]
    public function it_gets_webhook_logs(): void
    {
        $logsData = [
            [
                'id'          => 789,
                'webhook_id'  => 123,
                'event'       => 'vehicle.updated',
                'status_code' => 200,
            ],
            [
                'id'          => 790,
                'webhook_id'  => 123,
                'event'       => 'vehicle.location_updated',
                'status_code' => 500,
            ],
        ];

        $response = $this->createMockResponse(['webhook_logs' => $logsData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v2/webhooks/123/logs')
            ->willReturn($response);

        $resource = new WebhooksResource($client);
        $logs = $resource->logs(123);

        $this->assertCount(2, $logs);
        $this->assertInstanceOf(WebhookLog::class, $logs->first());
        $this->assertSame(789, $logs->first()->id);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new WebhooksResource($this->createStub(MotiveClient::class));

        $this->assertSame('webhooks', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new WebhooksResource($this->createStub(MotiveClient::class));

        $this->assertSame('webhook', $resource->getResourceKey());
    }

    #[Test]
    public function it_tests_webhook(): void
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('status')->willReturn(200);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v2/webhooks/123/test')
            ->willReturn($response);

        $resource = new WebhooksResource($client);
        $result = $resource->test(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_updates_webhook(): void
    {
        $webhookData = [
            'id'         => 123,
            'company_id' => 456,
            'url'        => 'https://example.com/new-url',
            'events'     => ['vehicle.updated'],
            'status'     => 'inactive',
        ];

        $response = $this->createMockResponse(['webhook' => $webhookData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('/v2/webhooks/123', ['webhook' => ['url' => 'https://example.com/new-url', 'status' => 'inactive']])
            ->willReturn($response);

        $resource = new WebhooksResource($client);
        $webhook = $resource->update(123, [
            'url'    => 'https://example.com/new-url',
            'status' => 'inactive',
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame(WebhookStatus::Inactive, $webhook->status);
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

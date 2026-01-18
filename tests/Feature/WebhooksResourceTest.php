<?php

namespace Motive\Tests\Feature;

use Motive\Data\Webhook;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use Motive\Data\WebhookLog;
use Motive\Enums\WebhookStatus;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Webhooks\WebhooksResource;

/**
 * Feature tests for WebhooksResource integration.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhooksResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_creates_webhook_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks' => Http::response([
                'webhook' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'url'        => 'https://example.com/webhook',
                    'secret'     => 'webhook-secret-123',
                    'events'     => ['vehicle.created', 'vehicle.updated'],
                    'status'     => 'active',
                    'created_at' => '2024-01-15T10:00:00Z',
                ],
            ], 201),
        ]);

        $webhook = Motive::webhooks()->create([
            'url'    => 'https://example.com/webhook',
            'events' => ['vehicle.created', 'vehicle.updated'],
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals(1, $webhook->id);
        $this->assertEquals('https://example.com/webhook', $webhook->url);
        $this->assertEquals(WebhookStatus::Active, $webhook->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v2/webhooks')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_deletes_webhook_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks/1' => Http::response([], 204),
        ]);

        $result = Motive::webhooks()->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_webhook_by_id_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks/1' => Http::response([
                'webhook' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'url'        => 'https://example.com/webhook',
                    'events'     => ['vehicle.created'],
                    'status'     => 'active',
                ],
            ], 200),
        ]);

        $webhook = Motive::webhooks()->find(1);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals(1, $webhook->id);
    }

    #[Test]
    public function it_gets_webhook_logs_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks/1/logs' => Http::response([
                'webhook_logs' => [
                    [
                        'id'          => 1,
                        'webhook_id'  => 1,
                        'event'       => 'vehicle.created',
                        'status_code' => 200,
                        'sent_at'     => '2024-01-15T12:00:00Z',
                    ],
                    [
                        'id'          => 2,
                        'webhook_id'  => 1,
                        'event'       => 'vehicle.updated',
                        'status_code' => 500,
                        'sent_at'     => '2024-01-15T12:05:00Z',
                    ],
                ],
            ], 200),
        ]);

        $logs = Motive::webhooks()->logs(1);

        $this->assertCount(2, $logs);
        $this->assertInstanceOf(WebhookLog::class, $logs[0]);
        $this->assertEquals(200, $logs[0]->statusCode);
    }

    #[Test]
    public function it_gets_webhooks_resource_from_manager(): void
    {
        $resource = Motive::webhooks();

        $this->assertInstanceOf(WebhooksResource::class, $resource);
    }

    #[Test]
    public function it_lists_webhooks_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks*' => Http::response([
                'webhooks' => [
                    [
                        'id'         => 1,
                        'company_id' => 100,
                        'url'        => 'https://example.com/webhook1',
                        'events'     => ['vehicle.created'],
                        'status'     => 'active',
                    ],
                    [
                        'id'         => 2,
                        'company_id' => 100,
                        'url'        => 'https://example.com/webhook2',
                        'events'     => ['driver.created'],
                        'status'     => 'inactive',
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 2,
                ],
            ], 200),
        ]);

        $webhooks = Motive::webhooks()->list();

        $this->assertCount(2, iterator_to_array($webhooks));
    }

    #[Test]
    public function it_tests_webhook_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks/1/test' => Http::response([], 200),
        ]);

        $result = Motive::webhooks()->test(1);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v2/webhooks/1/test')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_updates_webhook_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v2/webhooks/1' => Http::response([
                'webhook' => [
                    'id'         => 1,
                    'company_id' => 100,
                    'url'        => 'https://example.com/new-webhook',
                    'events'     => ['vehicle.created'],
                    'status'     => 'active',
                ],
            ], 200),
        ]);

        $webhook = Motive::webhooks()->update(1, ['url' => 'https://example.com/new-webhook']);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('https://example.com/new-webhook', $webhook->url);
    }
}

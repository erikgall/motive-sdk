<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Webhook;
use Carbon\CarbonImmutable;
use Motive\Enums\WebhookEvent;
use Motive\Enums\WebhookStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $webhook = Webhook::from([
            'id'         => 123,
            'company_id' => 456,
            'url'        => 'https://example.com/webhooks',
            'events'     => ['vehicle.updated'],
            'status'     => 'active',
        ]);

        $array = $webhook->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('https://example.com/webhooks', $array['url']);
        $this->assertSame('active', $array['status']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $webhook = Webhook::from([
            'id'         => 123,
            'company_id' => 456,
            'url'        => 'https://example.com/webhooks',
            'events'     => ['vehicle.location_updated', 'driver.hos_violation'],
            'status'     => 'active',
            'secret'     => 'webhook-secret',
            'created_at' => '2024-01-15T10:30:00Z',
            'updated_at' => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $webhook->id);
        $this->assertSame(456, $webhook->companyId);
        $this->assertSame('https://example.com/webhooks', $webhook->url);
        $this->assertCount(2, $webhook->events);
        $this->assertSame(WebhookEvent::VehicleLocationUpdated, $webhook->events[0]);
        $this->assertSame(WebhookEvent::DriverHosViolation, $webhook->events[1]);
        $this->assertSame(WebhookStatus::Active, $webhook->status);
        $this->assertSame('webhook-secret', $webhook->secret);
        $this->assertInstanceOf(CarbonImmutable::class, $webhook->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $webhook = Webhook::from([
            'id'         => 123,
            'company_id' => 456,
            'url'        => 'https://example.com/webhooks',
            'events'     => ['vehicle.updated'],
            'status'     => 'active',
        ]);

        $this->assertNull($webhook->secret);
        $this->assertNull($webhook->createdAt);
        $this->assertNull($webhook->updatedAt);
        $this->assertNull($webhook->description);
    }
}

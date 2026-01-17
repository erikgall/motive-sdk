<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\WebhookLog;
use Motive\Enums\WebhookEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookLogTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $log = WebhookLog::from([
            'id'          => 789,
            'webhook_id'  => 123,
            'event'       => 'vehicle.updated',
            'status_code' => 200,
        ]);

        $array = $log->toArray();

        $this->assertSame(789, $array['id']);
        $this->assertSame(123, $array['webhook_id']);
        $this->assertSame('vehicle.updated', $array['event']);
        $this->assertSame(200, $array['status_code']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $log = WebhookLog::from([
            'id'            => 789,
            'webhook_id'    => 123,
            'event'         => 'vehicle.location_updated',
            'status_code'   => 200,
            'response_body' => '{"received": true}',
            'request_body'  => '{"event": "vehicle.location_updated"}',
            'created_at'    => '2024-01-15T10:30:00Z',
        ]);

        $this->assertSame(789, $log->id);
        $this->assertSame(123, $log->webhookId);
        $this->assertSame(WebhookEvent::VehicleLocationUpdated, $log->event);
        $this->assertSame(200, $log->statusCode);
        $this->assertSame('{"received": true}', $log->responseBody);
        $this->assertInstanceOf(CarbonImmutable::class, $log->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $log = WebhookLog::from([
            'id'          => 789,
            'webhook_id'  => 123,
            'event'       => 'vehicle.updated',
            'status_code' => 500,
        ]);

        $this->assertNull($log->responseBody);
        $this->assertNull($log->requestBody);
        $this->assertNull($log->createdAt);
    }

    #[Test]
    public function it_identifies_failed_delivery(): void
    {
        $log = WebhookLog::from([
            'id'          => 789,
            'webhook_id'  => 123,
            'event'       => 'vehicle.updated',
            'status_code' => 500,
        ]);

        $this->assertFalse($log->isSuccessful());
    }

    #[Test]
    public function it_identifies_successful_delivery(): void
    {
        $log = WebhookLog::from([
            'id'          => 789,
            'webhook_id'  => 123,
            'event'       => 'vehicle.updated',
            'status_code' => 200,
        ]);

        $this->assertTrue($log->isSuccessful());
    }
}

<?php

namespace Motive\Tests\Unit\Webhooks;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Motive\Enums\WebhookEvent;
use PHPUnit\Framework\TestCase;
use Motive\Webhooks\WebhookPayload;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookPayloadTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $payload = WebhookPayload::from([
            'event'     => 'vehicle.updated',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => ['id' => 123],
        ]);

        $array = $payload->toArray();

        $this->assertSame('vehicle.updated', $array['event']);
        $this->assertArrayHasKey('timestamp', $array);
        $this->assertSame(['id' => 123], $array['data']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $payload = WebhookPayload::from([
            'event'     => 'vehicle.created',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => ['id' => 456],
        ]);

        $this->assertSame(WebhookEvent::VehicleCreated, $payload->event);
        $this->assertSame(456, $payload->data['id']);
    }

    #[Test]
    public function it_creates_from_request(): void
    {
        $request = $this->createMockRequest([
            'event'     => 'vehicle.location_updated',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => [
                'id'        => 123,
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $payload = WebhookPayload::fromRequest($request);

        $this->assertSame(WebhookEvent::VehicleLocationUpdated, $payload->event);
        $this->assertInstanceOf(CarbonImmutable::class, $payload->timestamp);
        $this->assertSame(123, $payload->data['id']);
    }

    #[Test]
    public function it_exposes_data(): void
    {
        $payload = WebhookPayload::from([
            'event'     => 'vehicle.updated',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => [
                'vehicle_id' => 789,
                'number'     => 'TRUCK-001',
                'status'     => 'active',
            ],
        ]);

        $this->assertSame(789, $payload->data['vehicle_id']);
        $this->assertSame('TRUCK-001', $payload->data['number']);
        $this->assertSame('active', $payload->data['status']);
    }

    #[Test]
    public function it_exposes_event_type(): void
    {
        $payload = WebhookPayload::from([
            'event'     => 'driver.hos_violation',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => [],
        ]);

        $this->assertSame(WebhookEvent::DriverHosViolation, $payload->event);
    }

    #[Test]
    public function it_exposes_timestamp(): void
    {
        $payload = WebhookPayload::from([
            'event'     => 'vehicle.updated',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => [],
        ]);

        $this->assertInstanceOf(CarbonImmutable::class, $payload->timestamp);
        $this->assertSame('2024-01-15 10:30:00', $payload->timestamp->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_returns_raw_payload(): void
    {
        $rawData = [
            'event'     => 'vehicle.updated',
            'timestamp' => '2024-01-15T10:30:00Z',
            'data'      => ['id' => 123],
        ];

        $payload = WebhookPayload::from($rawData);

        $this->assertSame($rawData, $payload->raw());
    }

    /**
     * Create a mock HTTP request with the given JSON body.
     *
     * @param  array<string, mixed>  $body
     */
    private function createMockRequest(array $body): Request
    {
        return Request::create(
            '/webhooks/motive',
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($body)
        );
    }
}

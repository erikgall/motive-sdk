<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\EventSeverity;
use PHPUnit\Framework\TestCase;
use Motive\Enums\PerformanceEventType;
use PHPUnit\Framework\Attributes\Test;
use Motive\Data\DriverPerformanceEvent;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DriverPerformanceEventTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $event = DriverPerformanceEvent::from([
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'event_type' => 'hard_braking',
            'severity'   => 'high',
            'speed'      => 65.5,
        ]);

        $array = $event->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(789, $array['driver_id']);
        $this->assertSame('hard_braking', $array['event_type']);
        $this->assertSame('high', $array['severity']);
        $this->assertSame(65.5, $array['speed']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $event = DriverPerformanceEvent::from([
            'id'          => 123,
            'company_id'  => 456,
            'driver_id'   => 789,
            'vehicle_id'  => 101,
            'event_type'  => 'hard_braking',
            'severity'    => 'high',
            'duration'    => 5,
            'speed'       => 65.5,
            'latitude'    => 34.0522,
            'longitude'   => -118.2437,
            'address'     => '123 Main St, Los Angeles, CA',
            'video_url'   => 'https://example.com/video.mp4',
            'occurred_at' => '2024-01-15T10:30:00Z',
            'created_at'  => '2024-01-15T10:30:05Z',
        ]);

        $this->assertSame(123, $event->id);
        $this->assertSame(456, $event->companyId);
        $this->assertSame(789, $event->driverId);
        $this->assertSame(101, $event->vehicleId);
        $this->assertSame(PerformanceEventType::HardBraking, $event->eventType);
        $this->assertSame(EventSeverity::High, $event->severity);
        $this->assertSame(5, $event->duration);
        $this->assertSame(65.5, $event->speed);
        $this->assertSame(34.0522, $event->latitude);
        $this->assertSame(-118.2437, $event->longitude);
        $this->assertSame('123 Main St, Los Angeles, CA', $event->address);
        $this->assertSame('https://example.com/video.mp4', $event->videoUrl);
        $this->assertInstanceOf(CarbonImmutable::class, $event->occurredAt);
        $this->assertInstanceOf(CarbonImmutable::class, $event->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $event = DriverPerformanceEvent::from([
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'event_type' => 'speeding',
            'severity'   => 'low',
        ]);

        $this->assertNull($event->vehicleId);
        $this->assertNull($event->duration);
        $this->assertNull($event->speed);
        $this->assertNull($event->latitude);
        $this->assertNull($event->longitude);
        $this->assertNull($event->address);
        $this->assertNull($event->videoUrl);
        $this->assertNull($event->occurredAt);
        $this->assertNull($event->createdAt);
    }
}

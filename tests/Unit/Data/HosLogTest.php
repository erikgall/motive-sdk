<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\HosLog;
use Carbon\CarbonImmutable;
use Motive\Enums\DutyStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosLogTest extends TestCase
{
    #[Test]
    public function it_creates_from_array(): void
    {
        $hosLog = HosLog::from([
            'id'          => 123,
            'driver_id'   => 456,
            'vehicle_id'  => 789,
            'duty_status' => 'driving',
            'start_time'  => '2024-01-15T08:00:00Z',
            'end_time'    => '2024-01-15T12:00:00Z',
            'duration'    => 14400,
            'latitude'    => 37.7749,
            'longitude'   => -122.4194,
            'location'    => 'San Francisco, CA',
            'odometer'    => 125000.5,
            'annotation'  => 'Started shift',
            'certified'   => false,
            'created_at'  => '2024-01-15T08:00:00Z',
            'updated_at'  => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $hosLog->id);
        $this->assertSame(456, $hosLog->driverId);
        $this->assertSame(789, $hosLog->vehicleId);
        $this->assertSame(DutyStatus::Driving, $hosLog->dutyStatus);
        $this->assertInstanceOf(CarbonImmutable::class, $hosLog->startTime);
        $this->assertInstanceOf(CarbonImmutable::class, $hosLog->endTime);
        $this->assertSame(14400, $hosLog->duration);
        $this->assertSame(37.7749, $hosLog->latitude);
        $this->assertSame(-122.4194, $hosLog->longitude);
        $this->assertSame('San Francisco, CA', $hosLog->location);
        $this->assertSame(125000.5, $hosLog->odometer);
        $this->assertSame('Started shift', $hosLog->annotation);
        $this->assertFalse($hosLog->certified);
        $this->assertInstanceOf(CarbonImmutable::class, $hosLog->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $hosLog->updatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $hosLog = HosLog::from([
            'id'          => 123,
            'driver_id'   => 456,
            'duty_status' => 'on_duty',
            'start_time'  => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $hosLog->id);
        $this->assertSame(456, $hosLog->driverId);
        $this->assertSame(DutyStatus::OnDuty, $hosLog->dutyStatus);
        $this->assertNull($hosLog->vehicleId);
        $this->assertNull($hosLog->endTime);
        $this->assertNull($hosLog->duration);
        $this->assertNull($hosLog->latitude);
        $this->assertNull($hosLog->longitude);
        $this->assertNull($hosLog->location);
        $this->assertNull($hosLog->annotation);
        $this->assertNull($hosLog->certified);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $hosLog = HosLog::from([
            'id'          => 123,
            'driver_id'   => 456,
            'duty_status' => 'driving',
            'start_time'  => '2024-01-15T08:00:00Z',
            'duration'    => 14400,
        ]);

        $array = $hosLog->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['driver_id']);
        $this->assertSame('driving', $array['duty_status']);
        $this->assertSame(14400, $array['duration']);
        $this->assertArrayHasKey('start_time', $array);
    }
}

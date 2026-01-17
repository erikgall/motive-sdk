<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\HosViolation;
use PHPUnit\Framework\TestCase;
use Motive\Enums\HosViolationType;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosViolationTest extends TestCase
{
    #[Test]
    public function it_creates_from_array(): void
    {
        $violation = HosViolation::from([
            'id'             => 123,
            'driver_id'      => 456,
            'vehicle_id'     => 789,
            'violation_type' => 'drive_time',
            'start_time'     => '2024-01-15T08:00:00Z',
            'end_time'       => '2024-01-15T16:00:00Z',
            'duration'       => 28800,
            'severity'       => 'critical',
            'location'       => 'San Francisco, CA',
            'created_at'     => '2024-01-15T16:00:00Z',
        ]);

        $this->assertSame(123, $violation->id);
        $this->assertSame(456, $violation->driverId);
        $this->assertSame(789, $violation->vehicleId);
        $this->assertSame(HosViolationType::DriveTime, $violation->violationType);
        $this->assertInstanceOf(CarbonImmutable::class, $violation->startTime);
        $this->assertInstanceOf(CarbonImmutable::class, $violation->endTime);
        $this->assertSame(28800, $violation->duration);
        $this->assertSame('critical', $violation->severity);
        $this->assertSame('San Francisco, CA', $violation->location);
        $this->assertInstanceOf(CarbonImmutable::class, $violation->createdAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $violation = HosViolation::from([
            'id'             => 123,
            'driver_id'      => 456,
            'violation_type' => 'break',
            'start_time'     => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $violation->id);
        $this->assertSame(456, $violation->driverId);
        $this->assertSame(HosViolationType::Break, $violation->violationType);
        $this->assertNull($violation->vehicleId);
        $this->assertNull($violation->endTime);
        $this->assertNull($violation->duration);
        $this->assertNull($violation->severity);
        $this->assertNull($violation->location);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $violation = HosViolation::from([
            'id'             => 123,
            'driver_id'      => 456,
            'violation_type' => 'cycle_time',
            'start_time'     => '2024-01-15T08:00:00Z',
            'duration'       => 3600,
        ]);

        $array = $violation->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['driver_id']);
        $this->assertSame('cycle_time', $array['violation_type']);
        $this->assertSame(3600, $array['duration']);
        $this->assertArrayHasKey('start_time', $array);
    }
}

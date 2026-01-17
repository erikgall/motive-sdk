<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Data\HosAvailability;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosAvailabilityTest extends TestCase
{
    #[Test]
    public function it_creates_from_array(): void
    {
        $availability = HosAvailability::from([
            'driver_id'           => 456,
            'drive_time_remaining' => 28800,
            'shift_time_remaining' => 50400,
            'cycle_time_remaining' => 252000,
            'break_time_required'  => 1800,
            'cycle_tomorrow'       => 259200,
            'recap'                => 7200,
            'last_calculated_at'   => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(456, $availability->driverId);
        $this->assertSame(28800, $availability->driveTimeRemaining);
        $this->assertSame(50400, $availability->shiftTimeRemaining);
        $this->assertSame(252000, $availability->cycleTimeRemaining);
        $this->assertSame(1800, $availability->breakTimeRequired);
        $this->assertSame(259200, $availability->cycleTomorrow);
        $this->assertSame(7200, $availability->recap);
        $this->assertInstanceOf(CarbonImmutable::class, $availability->lastCalculatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $availability = HosAvailability::from([
            'driver_id' => 456,
        ]);

        $this->assertSame(456, $availability->driverId);
        $this->assertNull($availability->driveTimeRemaining);
        $this->assertNull($availability->shiftTimeRemaining);
        $this->assertNull($availability->cycleTimeRemaining);
        $this->assertNull($availability->breakTimeRequired);
        $this->assertNull($availability->cycleTomorrow);
        $this->assertNull($availability->recap);
        $this->assertNull($availability->lastCalculatedAt);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $availability = HosAvailability::from([
            'driver_id'           => 456,
            'drive_time_remaining' => 28800,
            'shift_time_remaining' => 50400,
        ]);

        $array = $availability->toArray();

        $this->assertSame(456, $array['driver_id']);
        $this->assertSame(28800, $array['drive_time_remaining']);
        $this->assertSame(50400, $array['shift_time_remaining']);
    }
}

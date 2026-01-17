<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\UtilizationDay;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UtilizationDayTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $day = UtilizationDay::from([
            'date'            => '2024-01-15',
            'total_miles'     => 450.5,
            'idle_time_hours' => 1.25,
        ]);

        $array = $day->toArray();

        $this->assertSame('2024-01-15', $array['date']);
        $this->assertSame(450.5, $array['total_miles']);
        $this->assertSame(1.25, $array['idle_time_hours']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $day = UtilizationDay::from([
            'date'               => '2024-01-15',
            'total_miles'        => 450.5,
            'driving_time_hours' => 8.5,
            'idle_time_hours'    => 1.25,
            'stopped_time_hours' => 2.0,
            'fuel_used_gallons'  => 75.0,
            'average_speed'      => 53.0,
            'max_speed'          => 72.0,
        ]);

        $this->assertSame('2024-01-15', $day->date);
        $this->assertSame(450.5, $day->totalMiles);
        $this->assertSame(8.5, $day->drivingTimeHours);
        $this->assertSame(1.25, $day->idleTimeHours);
        $this->assertSame(2.0, $day->stoppedTimeHours);
        $this->assertSame(75.0, $day->fuelUsedGallons);
        $this->assertSame(53.0, $day->averageSpeed);
        $this->assertSame(72.0, $day->maxSpeed);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $day = UtilizationDay::from([
            'date'        => '2024-01-15',
            'total_miles' => 0.0,
        ]);

        $this->assertNull($day->drivingTimeHours);
        $this->assertNull($day->idleTimeHours);
        $this->assertNull($day->stoppedTimeHours);
        $this->assertNull($day->fuelUsedGallons);
        $this->assertNull($day->averageSpeed);
        $this->assertNull($day->maxSpeed);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\DrivingPeriod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DrivingPeriodTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $period = DrivingPeriod::from([
            'id'         => 123,
            'driver_id'  => 456,
            'vehicle_id' => 789,
            'start_time' => '2024-01-15T08:00:00Z',
            'distance'   => 150.5,
        ]);

        $array = $period->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['driver_id']);
        $this->assertSame(789, $array['vehicle_id']);
        $this->assertSame(150.5, $array['distance']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $period = DrivingPeriod::from([
            'id'         => 123,
            'driver_id'  => 456,
            'vehicle_id' => 789,
            'start_time' => '2024-01-15T08:00:00Z',
            'end_time'   => '2024-01-15T16:00:00Z',
        ]);

        $this->assertSame(123, $period->id);
        $this->assertSame(456, $period->driverId);
        $this->assertSame(789, $period->vehicleId);
        $this->assertInstanceOf(CarbonImmutable::class, $period->startTime);
        $this->assertInstanceOf(CarbonImmutable::class, $period->endTime);
    }

    #[Test]
    public function it_handles_distance_and_speed(): void
    {
        $period = DrivingPeriod::from([
            'id'            => 123,
            'driver_id'     => 456,
            'start_time'    => '2024-01-15T08:00:00Z',
            'distance'      => 150.5,
            'average_speed' => 55.2,
            'max_speed'     => 75.0,
        ]);

        $this->assertSame(150.5, $period->distance);
        $this->assertSame(55.2, $period->averageSpeed);
        $this->assertSame(75.0, $period->maxSpeed);
    }

    #[Test]
    public function it_handles_location_data(): void
    {
        $period = DrivingPeriod::from([
            'id'             => 123,
            'driver_id'      => 456,
            'start_time'     => '2024-01-15T08:00:00Z',
            'start_location' => ['lat' => 37.7749, 'lng' => -122.4194],
            'end_location'   => ['lat' => 34.0522, 'lng' => -118.2437],
        ]);

        $this->assertIsArray($period->startLocation);
        $this->assertSame(37.7749, $period->startLocation['lat']);
        $this->assertIsArray($period->endLocation);
        $this->assertSame(34.0522, $period->endLocation['lat']);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $period = DrivingPeriod::from([
            'id'         => 123,
            'driver_id'  => 456,
            'start_time' => '2024-01-15T08:00:00Z',
        ]);

        $this->assertNull($period->vehicleId);
        $this->assertNull($period->endTime);
        $this->assertNull($period->distance);
        $this->assertNull($period->averageSpeed);
        $this->assertNull($period->maxSpeed);
    }
}

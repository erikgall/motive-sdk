<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\ReeferActivity;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ReeferActivityTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $activity = ReeferActivity::from([
            'id'          => 123,
            'vehicle_id'  => 456,
            'temperature' => -5.5,
        ]);

        $array = $activity->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['vehicle_id']);
        $this->assertSame(-5.5, $array['temperature']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $activity = ReeferActivity::from([
            'id'             => 123,
            'vehicle_id'     => 456,
            'temperature'    => -5.5,
            'setpoint'       => -10.0,
            'mode'           => 'cooling',
            'fuel_level'     => 75.5,
            'engine_running' => true,
            'recorded_at'    => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $activity->id);
        $this->assertSame(456, $activity->vehicleId);
        $this->assertSame(-5.5, $activity->temperature);
        $this->assertSame(-10.0, $activity->setpoint);
        $this->assertSame('cooling', $activity->mode);
        $this->assertSame(75.5, $activity->fuelLevel);
        $this->assertTrue($activity->engineRunning);
        $this->assertInstanceOf(CarbonImmutable::class, $activity->recordedAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $activity = ReeferActivity::from([
            'id'         => 123,
            'vehicle_id' => 456,
        ]);

        $this->assertNull($activity->temperature);
        $this->assertNull($activity->setpoint);
        $this->assertNull($activity->mode);
        $this->assertNull($activity->fuelLevel);
        $this->assertFalse($activity->engineRunning);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\ShipmentEta;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ShipmentEtaTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $eta = ShipmentEta::from([
            'id'                 => 123,
            'shipment_id'        => 456,
            'distance_remaining' => 150.5,
            'time_remaining'     => 7200,
        ]);

        $array = $eta->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['shipment_id']);
        $this->assertSame(150.5, $array['distance_remaining']);
        $this->assertSame(7200, $array['time_remaining']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $eta = ShipmentEta::from([
            'id'                 => 123,
            'shipment_id'        => 456,
            'estimated_arrival'  => '2024-01-15T18:00:00Z',
            'distance_remaining' => 150.5,
            'time_remaining'     => 7200,
            'confidence'         => 0.95,
        ]);

        $this->assertSame(123, $eta->id);
        $this->assertSame(456, $eta->shipmentId);
        $this->assertInstanceOf(CarbonImmutable::class, $eta->estimatedArrival);
        $this->assertSame(150.5, $eta->distanceRemaining);
        $this->assertSame(7200, $eta->timeRemaining);
        $this->assertSame(0.95, $eta->confidence);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $eta = ShipmentEta::from([
            'id'          => 123,
            'shipment_id' => 456,
        ]);

        $this->assertNull($eta->estimatedArrival);
        $this->assertNull($eta->distanceRemaining);
        $this->assertNull($eta->timeRemaining);
        $this->assertNull($eta->confidence);
    }
}

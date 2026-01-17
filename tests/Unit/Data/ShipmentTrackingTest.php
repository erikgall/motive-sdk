<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Data\ShipmentTracking;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ShipmentTrackingTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $tracking = ShipmentTracking::from([
            'id'          => 123,
            'shipment_id' => 456,
            'speed'       => 65.5,
        ]);

        $array = $tracking->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['shipment_id']);
        $this->assertSame(65.5, $array['speed']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $tracking = ShipmentTracking::from([
            'id'               => 123,
            'shipment_id'      => 456,
            'current_location' => ['lat' => 37.7749, 'lng' => -122.4194],
            'last_update'      => '2024-01-15T14:30:00Z',
            'speed'            => 65.5,
            'heading'          => 45,
        ]);

        $this->assertSame(123, $tracking->id);
        $this->assertSame(456, $tracking->shipmentId);
        $this->assertIsArray($tracking->currentLocation);
        $this->assertSame(37.7749, $tracking->currentLocation['lat']);
        $this->assertInstanceOf(CarbonImmutable::class, $tracking->lastUpdate);
        $this->assertSame(65.5, $tracking->speed);
        $this->assertSame(45, $tracking->heading);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $tracking = ShipmentTracking::from([
            'id'          => 123,
            'shipment_id' => 456,
        ]);

        $this->assertNull($tracking->currentLocation);
        $this->assertNull($tracking->lastUpdate);
        $this->assertNull($tracking->speed);
        $this->assertNull($tracking->heading);
        $this->assertNull($tracking->distanceRemaining);
    }
}

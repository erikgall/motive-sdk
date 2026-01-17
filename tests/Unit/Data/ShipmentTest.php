<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Shipment;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Enums\ShipmentStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ShipmentTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $shipment = Shipment::from([
            'id'           => 123,
            'reference_id' => 'SHIP-001',
            'status'       => 'in_transit',
            'driver_id'    => 456,
        ]);

        $array = $shipment->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('SHIP-001', $array['reference_id']);
        $this->assertSame('in_transit', $array['status']);
        $this->assertSame(456, $array['driver_id']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $shipment = Shipment::from([
            'id'                => 123,
            'reference_id'      => 'SHIP-001',
            'status'            => 'in_transit',
            'origin'            => 'Los Angeles, CA',
            'destination'       => 'San Francisco, CA',
            'estimated_arrival' => '2024-01-15T18:00:00Z',
            'created_at'        => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $shipment->id);
        $this->assertSame('SHIP-001', $shipment->referenceId);
        $this->assertSame(ShipmentStatus::InTransit, $shipment->status);
        $this->assertSame('Los Angeles, CA', $shipment->origin);
        $this->assertSame('San Francisco, CA', $shipment->destination);
        $this->assertInstanceOf(CarbonImmutable::class, $shipment->estimatedArrival);
        $this->assertInstanceOf(CarbonImmutable::class, $shipment->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $shipment = Shipment::from([
            'id'     => 123,
            'status' => 'pending',
        ]);

        $this->assertNull($shipment->referenceId);
        $this->assertNull($shipment->origin);
        $this->assertNull($shipment->destination);
        $this->assertNull($shipment->driverId);
        $this->assertNull($shipment->vehicleId);
    }
}

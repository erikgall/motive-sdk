<?php

namespace Motive\Tests\Unit\Data;

use Motive\Enums\StopType;
use Carbon\CarbonImmutable;
use Motive\Data\DispatchStop;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchStopTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $stop = DispatchStop::from([
            'id'          => 123,
            'dispatch_id' => 456,
            'stop_type'   => 'waypoint',
            'name'        => 'Rest Area',
        ]);

        $array = $stop->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['dispatch_id']);
        $this->assertSame('waypoint', $array['stop_type']);
        $this->assertSame('Rest Area', $array['name']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $stop = DispatchStop::from([
            'id'           => 123,
            'dispatch_id'  => 456,
            'stop_type'    => 'pickup',
            'name'         => 'Warehouse A',
            'address'      => '123 Main St',
            'city'         => 'San Francisco',
            'state'        => 'CA',
            'postal_code'  => '94105',
            'country'      => 'US',
            'latitude'     => 37.7749,
            'longitude'    => -122.4194,
            'scheduled_at' => '2024-01-15T08:00:00Z',
            'arrived_at'   => '2024-01-15T08:15:00Z',
            'departed_at'  => '2024-01-15T09:00:00Z',
            'notes'        => 'Ring bell for delivery',
            'sequence'     => 1,
            'created_at'   => '2024-01-10T10:00:00Z',
        ]);

        $this->assertSame(123, $stop->id);
        $this->assertSame(456, $stop->dispatchId);
        $this->assertSame(StopType::Pickup, $stop->stopType);
        $this->assertSame('Warehouse A', $stop->name);
        $this->assertSame('123 Main St', $stop->address);
        $this->assertSame('San Francisco', $stop->city);
        $this->assertSame('CA', $stop->state);
        $this->assertSame('94105', $stop->postalCode);
        $this->assertSame('US', $stop->country);
        $this->assertSame(37.7749, $stop->latitude);
        $this->assertSame(-122.4194, $stop->longitude);
        $this->assertInstanceOf(CarbonImmutable::class, $stop->scheduledAt);
        $this->assertInstanceOf(CarbonImmutable::class, $stop->arrivedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $stop->departedAt);
        $this->assertSame('Ring bell for delivery', $stop->notes);
        $this->assertSame(1, $stop->sequence);
        $this->assertInstanceOf(CarbonImmutable::class, $stop->createdAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $stop = DispatchStop::from([
            'id'          => 123,
            'dispatch_id' => 456,
            'stop_type'   => 'delivery',
        ]);

        $this->assertSame(123, $stop->id);
        $this->assertSame(456, $stop->dispatchId);
        $this->assertSame(StopType::Delivery, $stop->stopType);
        $this->assertNull($stop->name);
        $this->assertNull($stop->address);
        $this->assertNull($stop->city);
        $this->assertNull($stop->state);
        $this->assertNull($stop->postalCode);
        $this->assertNull($stop->country);
        $this->assertNull($stop->latitude);
        $this->assertNull($stop->longitude);
        $this->assertNull($stop->scheduledAt);
        $this->assertNull($stop->arrivedAt);
        $this->assertNull($stop->departedAt);
    }
}

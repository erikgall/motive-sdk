<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Data\VehicleLocation;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleLocationTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $location = VehicleLocation::from([
            'latitude'   => 37.7749,
            'longitude'  => -122.4194,
            'speed'      => 55.5,
            'bearing'    => 180,
            'address'    => '123 Main St',
            'located_at' => '2024-01-15T10:30:00Z',
        ]);

        $array = $location->toArray();

        $this->assertSame(37.7749, $array['latitude']);
        $this->assertSame(-122.4194, $array['longitude']);
        $this->assertSame(55.5, $array['speed']);
        $this->assertSame(180, $array['bearing']);
        $this->assertSame('123 Main St', $array['address']);
        $this->assertArrayHasKey('located_at', $array);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $location = VehicleLocation::from([
            'latitude'   => 37.7749,
            'longitude'  => -122.4194,
            'speed'      => 55.5,
            'bearing'    => 180,
            'address'    => '123 Main St, San Francisco, CA',
            'located_at' => '2024-01-15T10:30:00Z',
        ]);

        $this->assertSame(37.7749, $location->latitude);
        $this->assertSame(-122.4194, $location->longitude);
        $this->assertSame(55.5, $location->speed);
        $this->assertSame(180, $location->bearing);
        $this->assertSame('123 Main St, San Francisco, CA', $location->address);
        $this->assertInstanceOf(CarbonImmutable::class, $location->locatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $location = VehicleLocation::from([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ]);

        $this->assertSame(37.7749, $location->latitude);
        $this->assertSame(-122.4194, $location->longitude);
        $this->assertNull($location->speed);
        $this->assertNull($location->bearing);
        $this->assertNull($location->address);
        $this->assertNull($location->locatedAt);
    }
}

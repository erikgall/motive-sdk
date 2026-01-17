<?php

namespace Motive\Tests\Unit\Data;

use PHPUnit\Framework\TestCase;
use Motive\Data\GeofenceCoordinate;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofenceCoordinateTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $coordinate = GeofenceCoordinate::from([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
            'sequence'  => 2,
        ]);

        $array = $coordinate->toArray();

        $this->assertSame(37.7749, $array['latitude']);
        $this->assertSame(-122.4194, $array['longitude']);
        $this->assertSame(2, $array['sequence']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $coordinate = GeofenceCoordinate::from([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
            'sequence'  => 1,
        ]);

        $this->assertSame(37.7749, $coordinate->latitude);
        $this->assertSame(-122.4194, $coordinate->longitude);
        $this->assertSame(1, $coordinate->sequence);
    }

    #[Test]
    public function it_handles_optional_sequence(): void
    {
        $coordinate = GeofenceCoordinate::from([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ]);

        $this->assertSame(37.7749, $coordinate->latitude);
        $this->assertSame(-122.4194, $coordinate->longitude);
        $this->assertNull($coordinate->sequence);
    }
}

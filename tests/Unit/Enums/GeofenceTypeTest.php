<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\GeofenceType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofenceTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(GeofenceType::Circle, GeofenceType::from('circle'));
        $this->assertSame(GeofenceType::Polygon, GeofenceType::from('polygon'));
    }

    #[Test]
    public function it_has_circle_type(): void
    {
        $this->assertSame('circle', GeofenceType::Circle->value);
    }

    #[Test]
    public function it_has_polygon_type(): void
    {
        $this->assertSame('polygon', GeofenceType::Polygon->value);
    }
}

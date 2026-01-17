<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\StopType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class StopTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(StopType::Pickup, StopType::from('pickup'));
        $this->assertSame(StopType::Delivery, StopType::from('delivery'));
        $this->assertSame(StopType::Waypoint, StopType::from('waypoint'));
    }

    #[Test]
    public function it_has_delivery_type(): void
    {
        $this->assertSame('delivery', StopType::Delivery->value);
    }

    #[Test]
    public function it_has_pickup_type(): void
    {
        $this->assertSame('pickup', StopType::Pickup->value);
    }

    #[Test]
    public function it_has_waypoint_type(): void
    {
        $this->assertSame('waypoint', StopType::Waypoint->value);
    }
}

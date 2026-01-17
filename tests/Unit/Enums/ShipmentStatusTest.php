<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\ShipmentStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ShipmentStatusTest extends TestCase
{
    #[Test]
    public function it_creates_from_value(): void
    {
        $this->assertSame(ShipmentStatus::Pending, ShipmentStatus::from('pending'));
        $this->assertSame(ShipmentStatus::InTransit, ShipmentStatus::from('in_transit'));
        $this->assertSame(ShipmentStatus::Delivered, ShipmentStatus::from('delivered'));
    }

    #[Test]
    public function it_has_expected_cases(): void
    {
        $cases = ShipmentStatus::cases();

        $this->assertContains(ShipmentStatus::Pending, $cases);
        $this->assertContains(ShipmentStatus::InTransit, $cases);
        $this->assertContains(ShipmentStatus::Delivered, $cases);
        $this->assertContains(ShipmentStatus::Cancelled, $cases);
    }
}

<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\VehicleStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(VehicleStatus::Active, VehicleStatus::from('active'));
        $this->assertSame(VehicleStatus::Inactive, VehicleStatus::from('inactive'));
        $this->assertSame(VehicleStatus::Decommissioned, VehicleStatus::from('decommissioned'));
    }

    #[Test]
    public function it_has_active_status(): void
    {
        $this->assertSame('active', VehicleStatus::Active->value);
    }

    #[Test]
    public function it_has_decommissioned_status(): void
    {
        $this->assertSame('decommissioned', VehicleStatus::Decommissioned->value);
    }

    #[Test]
    public function it_has_inactive_status(): void
    {
        $this->assertSame('inactive', VehicleStatus::Inactive->value);
    }
}

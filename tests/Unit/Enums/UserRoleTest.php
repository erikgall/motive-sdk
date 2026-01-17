<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\UserRole;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UserRoleTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(UserRole::Admin, UserRole::from('admin'));
        $this->assertSame(UserRole::Driver, UserRole::from('driver'));
    }

    #[Test]
    public function it_has_admin_role(): void
    {
        $this->assertSame('admin', UserRole::Admin->value);
    }

    #[Test]
    public function it_has_dispatcher_role(): void
    {
        $this->assertSame('dispatcher', UserRole::Dispatcher->value);
    }

    #[Test]
    public function it_has_driver_role(): void
    {
        $this->assertSame('driver', UserRole::Driver->value);
    }

    #[Test]
    public function it_has_fleet_manager_role(): void
    {
        $this->assertSame('fleet_manager', UserRole::FleetManager->value);
    }

    #[Test]
    public function it_has_mechanic_role(): void
    {
        $this->assertSame('mechanic', UserRole::Mechanic->value);
    }
}

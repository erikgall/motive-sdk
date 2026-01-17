<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\Scope;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ScopeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $scope = Scope::from('vehicles.read');

        $this->assertSame(Scope::VehiclesRead, $scope);
    }

    #[Test]
    public function it_has_assets_read_scope(): void
    {
        $this->assertSame('assets.read', Scope::AssetsRead->value);
    }

    #[Test]
    public function it_has_assets_write_scope(): void
    {
        $this->assertSame('assets.write', Scope::AssetsWrite->value);
    }

    #[Test]
    public function it_has_dispatches_read_scope(): void
    {
        $this->assertSame('dispatches.read', Scope::DispatchesRead->value);
    }

    #[Test]
    public function it_has_dispatches_write_scope(): void
    {
        $this->assertSame('dispatches.write', Scope::DispatchesWrite->value);
    }

    #[Test]
    public function it_has_drivers_read_scope(): void
    {
        $this->assertSame('drivers.read', Scope::DriversRead->value);
    }

    #[Test]
    public function it_has_drivers_write_scope(): void
    {
        $this->assertSame('drivers.write', Scope::DriversWrite->value);
    }

    #[Test]
    public function it_has_hos_read_scope(): void
    {
        $this->assertSame('hos.read', Scope::HosRead->value);
    }

    #[Test]
    public function it_has_hos_write_scope(): void
    {
        $this->assertSame('hos.write', Scope::HosWrite->value);
    }

    #[Test]
    public function it_has_locations_read_scope(): void
    {
        $this->assertSame('locations.read', Scope::LocationsRead->value);
    }

    #[Test]
    public function it_has_locations_write_scope(): void
    {
        $this->assertSame('locations.write', Scope::LocationsWrite->value);
    }

    #[Test]
    public function it_has_users_read_scope(): void
    {
        $this->assertSame('users.read', Scope::UsersRead->value);
    }

    #[Test]
    public function it_has_users_write_scope(): void
    {
        $this->assertSame('users.write', Scope::UsersWrite->value);
    }

    #[Test]
    public function it_has_vehicles_read_scope(): void
    {
        $this->assertSame('vehicles.read', Scope::VehiclesRead->value);
    }

    #[Test]
    public function it_has_vehicles_write_scope(): void
    {
        $this->assertSame('vehicles.write', Scope::VehiclesWrite->value);
    }

    #[Test]
    public function it_has_webhooks_read_scope(): void
    {
        $this->assertSame('webhooks.read', Scope::WebhooksRead->value);
    }

    #[Test]
    public function it_has_webhooks_write_scope(): void
    {
        $this->assertSame('webhooks.write', Scope::WebhooksWrite->value);
    }
}

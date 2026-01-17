<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\AssetType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AssetTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(AssetType::Trailer, AssetType::from('trailer'));
        $this->assertSame(AssetType::Container, AssetType::from('container'));
    }

    #[Test]
    public function it_has_chassis_type(): void
    {
        $this->assertSame('chassis', AssetType::Chassis->value);
    }

    #[Test]
    public function it_has_container_type(): void
    {
        $this->assertSame('container', AssetType::Container->value);
    }

    #[Test]
    public function it_has_equipment_type(): void
    {
        $this->assertSame('equipment', AssetType::Equipment->value);
    }

    #[Test]
    public function it_has_trailer_type(): void
    {
        $this->assertSame('trailer', AssetType::Trailer->value);
    }
}

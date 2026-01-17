<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\AssetStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AssetStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(AssetStatus::Active, AssetStatus::from('active'));
        $this->assertSame(AssetStatus::Inactive, AssetStatus::from('inactive'));
    }

    #[Test]
    public function it_has_active_status(): void
    {
        $this->assertSame('active', AssetStatus::Active->value);
    }

    #[Test]
    public function it_has_inactive_status(): void
    {
        $this->assertSame('inactive', AssetStatus::Inactive->value);
    }
}

<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\UserStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UserStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(UserStatus::Active, UserStatus::from('active'));
        $this->assertSame(UserStatus::Inactive, UserStatus::from('inactive'));
        $this->assertSame(UserStatus::Pending, UserStatus::from('pending'));
    }

    #[Test]
    public function it_has_active_status(): void
    {
        $this->assertSame('active', UserStatus::Active->value);
    }

    #[Test]
    public function it_has_inactive_status(): void
    {
        $this->assertSame('inactive', UserStatus::Inactive->value);
    }

    #[Test]
    public function it_has_pending_status(): void
    {
        $this->assertSame('pending', UserStatus::Pending->value);
    }
}

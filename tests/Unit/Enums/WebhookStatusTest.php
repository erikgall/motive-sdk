<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\WebhookStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $status = WebhookStatus::from('active');

        $this->assertSame(WebhookStatus::Active, $status);
    }

    #[Test]
    public function it_has_active_status(): void
    {
        $this->assertSame('active', WebhookStatus::Active->value);
    }

    #[Test]
    public function it_has_inactive_status(): void
    {
        $this->assertSame('inactive', WebhookStatus::Inactive->value);
    }

    #[Test]
    public function it_has_suspended_status(): void
    {
        $this->assertSame('suspended', WebhookStatus::Suspended->value);
    }
}

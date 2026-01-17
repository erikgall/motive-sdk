<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\MessageDirection;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MessageDirectionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $direction = MessageDirection::from('inbound');

        $this->assertSame(MessageDirection::Inbound, $direction);
    }

    #[Test]
    public function it_has_inbound_direction(): void
    {
        $this->assertSame('inbound', MessageDirection::Inbound->value);
    }

    #[Test]
    public function it_has_outbound_direction(): void
    {
        $this->assertSame('outbound', MessageDirection::Outbound->value);
    }
}

<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\EventSeverity;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class EventSeverityTest extends TestCase
{
    #[Test]
    public function it_creates_from_value(): void
    {
        $severity = EventSeverity::from('high');

        $this->assertSame(EventSeverity::High, $severity);
    }

    #[Test]
    public function it_has_expected_cases(): void
    {
        $this->assertSame('low', EventSeverity::Low->value);
        $this->assertSame('medium', EventSeverity::Medium->value);
        $this->assertSame('high', EventSeverity::High->value);
        $this->assertSame('critical', EventSeverity::Critical->value);
    }
}

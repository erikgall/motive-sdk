<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\TimecardStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class TimecardStatusTest extends TestCase
{
    #[Test]
    public function it_creates_from_value(): void
    {
        $status = TimecardStatus::from('approved');

        $this->assertSame(TimecardStatus::Approved, $status);
    }

    #[Test]
    public function it_has_expected_cases(): void
    {
        $this->assertSame('pending', TimecardStatus::Pending->value);
        $this->assertSame('approved', TimecardStatus::Approved->value);
        $this->assertSame('rejected', TimecardStatus::Rejected->value);
        $this->assertSame('submitted', TimecardStatus::Submitted->value);
    }
}

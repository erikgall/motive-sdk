<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\HosViolationType;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosViolationTypeTest extends TestCase
{
    #[Test]
    public function it_has_drive_time_violation(): void
    {
        $this->assertSame('drive_time', HosViolationType::DriveTime->value);
    }

    #[Test]
    public function it_has_shift_time_violation(): void
    {
        $this->assertSame('shift_time', HosViolationType::ShiftTime->value);
    }

    #[Test]
    public function it_has_cycle_time_violation(): void
    {
        $this->assertSame('cycle_time', HosViolationType::CycleTime->value);
    }

    #[Test]
    public function it_has_break_violation(): void
    {
        $this->assertSame('break', HosViolationType::Break->value);
    }

    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(HosViolationType::DriveTime, HosViolationType::from('drive_time'));
        $this->assertSame(HosViolationType::Break, HosViolationType::from('break'));
    }
}

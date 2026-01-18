<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\DutyStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DutyStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(DutyStatus::OffDuty, DutyStatus::from('off_duty'));
        $this->assertSame(DutyStatus::Driving, DutyStatus::from('driving'));
        $this->assertSame(DutyStatus::OnDuty, DutyStatus::from('on_duty'));
    }

    #[Test]
    public function it_has_driving_status(): void
    {
        $this->assertSame('driving', DutyStatus::Driving->value);
    }

    #[Test]
    public function it_has_off_duty_status(): void
    {
        $this->assertSame('off_duty', DutyStatus::OffDuty->value);
    }

    #[Test]
    public function it_has_on_duty_status(): void
    {
        $this->assertSame('on_duty', DutyStatus::OnDuty->value);
    }

    #[Test]
    public function it_has_personal_conveyance_status(): void
    {
        $this->assertSame('personal_conveyance', DutyStatus::PersonalConveyance->value);
    }

    #[Test]
    public function it_has_sleeper_berth_status(): void
    {
        $this->assertSame('sleeper_berth', DutyStatus::SleeperBerth->value);
    }

    #[Test]
    public function it_has_yard_move_status(): void
    {
        $this->assertSame('yard_move', DutyStatus::YardMove->value);
    }
}

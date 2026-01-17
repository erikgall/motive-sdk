<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\DispatchStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(DispatchStatus::Pending, DispatchStatus::from('pending'));
        $this->assertSame(DispatchStatus::InProgress, DispatchStatus::from('in_progress'));
        $this->assertSame(DispatchStatus::Completed, DispatchStatus::from('completed'));
        $this->assertSame(DispatchStatus::Cancelled, DispatchStatus::from('cancelled'));
    }

    #[Test]
    public function it_has_cancelled_status(): void
    {
        $this->assertSame('cancelled', DispatchStatus::Cancelled->value);
    }

    #[Test]
    public function it_has_completed_status(): void
    {
        $this->assertSame('completed', DispatchStatus::Completed->value);
    }

    #[Test]
    public function it_has_in_progress_status(): void
    {
        $this->assertSame('in_progress', DispatchStatus::InProgress->value);
    }

    #[Test]
    public function it_has_pending_status(): void
    {
        $this->assertSame('pending', DispatchStatus::Pending->value);
    }
}

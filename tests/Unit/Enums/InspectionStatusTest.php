<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\InspectionStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(InspectionStatus::Passed, InspectionStatus::from('passed'));
        $this->assertSame(InspectionStatus::Failed, InspectionStatus::from('failed'));
        $this->assertSame(InspectionStatus::Corrected, InspectionStatus::from('corrected'));
        $this->assertSame(InspectionStatus::Satisfactory, InspectionStatus::from('satisfactory'));
    }

    #[Test]
    public function it_has_corrected_status(): void
    {
        $this->assertSame('corrected', InspectionStatus::Corrected->value);
    }

    #[Test]
    public function it_has_failed_status(): void
    {
        $this->assertSame('failed', InspectionStatus::Failed->value);
    }

    #[Test]
    public function it_has_passed_status(): void
    {
        $this->assertSame('passed', InspectionStatus::Passed->value);
    }

    #[Test]
    public function it_has_satisfactory_status(): void
    {
        $this->assertSame('satisfactory', InspectionStatus::Satisfactory->value);
    }
}

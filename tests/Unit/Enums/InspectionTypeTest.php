<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\InspectionType;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string_value(): void
    {
        $this->assertSame(InspectionType::PreTrip, InspectionType::from('pre_trip'));
        $this->assertSame(InspectionType::PostTrip, InspectionType::from('post_trip'));
        $this->assertSame(InspectionType::Dot, InspectionType::from('dot'));
    }

    #[Test]
    public function it_has_dot_type(): void
    {
        $this->assertSame('dot', InspectionType::Dot->value);
    }

    #[Test]
    public function it_has_post_trip_type(): void
    {
        $this->assertSame('post_trip', InspectionType::PostTrip->value);
    }

    #[Test]
    public function it_has_pre_trip_type(): void
    {
        $this->assertSame('pre_trip', InspectionType::PreTrip->value);
    }
}

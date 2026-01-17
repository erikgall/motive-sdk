<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\PerformanceEventType;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class PerformanceEventTypeTest extends TestCase
{
    #[Test]
    public function it_creates_from_value(): void
    {
        $type = PerformanceEventType::from('hard_braking');

        $this->assertSame(PerformanceEventType::HardBraking, $type);
    }

    #[Test]
    public function it_has_expected_cases(): void
    {
        $this->assertSame('hard_braking', PerformanceEventType::HardBraking->value);
        $this->assertSame('rapid_acceleration', PerformanceEventType::RapidAcceleration->value);
        $this->assertSame('speeding', PerformanceEventType::Speeding->value);
        $this->assertSame('harsh_cornering', PerformanceEventType::HarshCornering->value);
        $this->assertSame('lane_departure', PerformanceEventType::LaneDeparture->value);
        $this->assertSame('following_distance', PerformanceEventType::FollowingDistance->value);
        $this->assertSame('collision_warning', PerformanceEventType::CollisionWarning->value);
        $this->assertSame('distracted_driving', PerformanceEventType::DistractedDriving->value);
        $this->assertSame('drowsy_driving', PerformanceEventType::DrowsyDriving->value);
        $this->assertSame('phone_usage', PerformanceEventType::PhoneUsage->value);
        $this->assertSame('seatbelt', PerformanceEventType::Seatbelt->value);
        $this->assertSame('stop_sign_violation', PerformanceEventType::StopSignViolation->value);
    }
}

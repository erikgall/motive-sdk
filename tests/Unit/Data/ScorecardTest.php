<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Scorecard;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ScorecardTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $scorecard = Scorecard::from([
            'id'            => 123,
            'company_id'    => 456,
            'driver_id'     => 789,
            'overall_score' => 85.5,
            'safety_score'  => 90.0,
        ]);

        $array = $scorecard->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(789, $array['driver_id']);
        $this->assertSame(85.5, $array['overall_score']);
        $this->assertSame(90.0, $array['safety_score']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $scorecard = Scorecard::from([
            'id'                  => 123,
            'company_id'          => 456,
            'driver_id'           => 789,
            'overall_score'       => 85.5,
            'safety_score'        => 90.0,
            'efficiency_score'    => 80.0,
            'compliance_score'    => 88.0,
            'total_miles'         => 2500.5,
            'total_events'        => 15,
            'hard_braking_events' => 5,
            'speeding_events'     => 3,
            'rapid_accel_events'  => 2,
            'idle_time_minutes'   => 120,
            'period_start'        => '2024-01-01T00:00:00Z',
            'period_end'          => '2024-01-31T23:59:59Z',
            'created_at'          => '2024-02-01T10:00:00Z',
        ]);

        $this->assertSame(123, $scorecard->id);
        $this->assertSame(456, $scorecard->companyId);
        $this->assertSame(789, $scorecard->driverId);
        $this->assertSame(85.5, $scorecard->overallScore);
        $this->assertSame(90.0, $scorecard->safetyScore);
        $this->assertSame(80.0, $scorecard->efficiencyScore);
        $this->assertSame(88.0, $scorecard->complianceScore);
        $this->assertSame(2500.5, $scorecard->totalMiles);
        $this->assertSame(15, $scorecard->totalEvents);
        $this->assertSame(5, $scorecard->hardBrakingEvents);
        $this->assertSame(3, $scorecard->speedingEvents);
        $this->assertSame(2, $scorecard->rapidAccelEvents);
        $this->assertSame(120, $scorecard->idleTimeMinutes);
        $this->assertInstanceOf(CarbonImmutable::class, $scorecard->periodStart);
        $this->assertInstanceOf(CarbonImmutable::class, $scorecard->periodEnd);
        $this->assertInstanceOf(CarbonImmutable::class, $scorecard->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $scorecard = Scorecard::from([
            'id'            => 123,
            'company_id'    => 456,
            'overall_score' => 85.5,
        ]);

        $this->assertNull($scorecard->driverId);
        $this->assertNull($scorecard->safetyScore);
        $this->assertNull($scorecard->efficiencyScore);
        $this->assertNull($scorecard->complianceScore);
        $this->assertNull($scorecard->totalMiles);
        $this->assertNull($scorecard->totalEvents);
        $this->assertNull($scorecard->hardBrakingEvents);
        $this->assertNull($scorecard->speedingEvents);
        $this->assertNull($scorecard->rapidAccelEvents);
        $this->assertNull($scorecard->idleTimeMinutes);
        $this->assertNull($scorecard->periodStart);
        $this->assertNull($scorecard->periodEnd);
        $this->assertNull($scorecard->createdAt);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\UtilizationDay;
use PHPUnit\Framework\TestCase;
use Motive\Data\UtilizationReport;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UtilizationReportTest extends TestCase
{
    #[Test]
    public function it_casts_daily_utilization_array(): void
    {
        $report = UtilizationReport::from([
            'id'                => 123,
            'company_id'        => 456,
            'daily_utilization' => [
                ['date' => '2024-01-01', 'total_miles' => 450.0],
                ['date' => '2024-01-02', 'total_miles' => 475.0],
            ],
        ]);

        $this->assertCount(2, $report->dailyUtilization);
        $this->assertInstanceOf(UtilizationDay::class, $report->dailyUtilization[0]);
        $this->assertSame('2024-01-01', $report->dailyUtilization[0]->date);
        $this->assertSame(475.0, $report->dailyUtilization[1]->totalMiles);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $report = UtilizationReport::from([
            'id'          => 123,
            'company_id'  => 456,
            'vehicle_id'  => 789,
            'total_miles' => 5500.75,
        ]);

        $array = $report->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(789, $array['vehicle_id']);
        $this->assertSame(5500.75, $array['total_miles']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $report = UtilizationReport::from([
            'id'                       => 123,
            'company_id'               => 456,
            'vehicle_id'               => 789,
            'total_miles'              => 5500.75,
            'total_driving_time_hours' => 120.5,
            'total_idle_time_hours'    => 15.25,
            'total_stopped_time_hours' => 30.0,
            'total_fuel_used_gallons'  => 950.5,
            'average_miles_per_day'    => 183.4,
            'average_speed'            => 45.6,
            'utilization_percentage'   => 78.5,
            'start_date'               => '2024-01-01',
            'end_date'                 => '2024-01-31',
        ]);

        $this->assertSame(123, $report->id);
        $this->assertSame(456, $report->companyId);
        $this->assertSame(789, $report->vehicleId);
        $this->assertSame(5500.75, $report->totalMiles);
        $this->assertSame(120.5, $report->totalDrivingTimeHours);
        $this->assertSame(15.25, $report->totalIdleTimeHours);
        $this->assertSame(30.0, $report->totalStoppedTimeHours);
        $this->assertSame(950.5, $report->totalFuelUsedGallons);
        $this->assertSame(183.4, $report->averageMilesPerDay);
        $this->assertSame(45.6, $report->averageSpeed);
        $this->assertSame(78.5, $report->utilizationPercentage);
        $this->assertSame('2024-01-01', $report->startDate);
        $this->assertSame('2024-01-31', $report->endDate);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $report = UtilizationReport::from([
            'id'         => 123,
            'company_id' => 456,
        ]);

        $this->assertNull($report->vehicleId);
        $this->assertNull($report->totalMiles);
        $this->assertNull($report->totalDrivingTimeHours);
        $this->assertNull($report->totalIdleTimeHours);
        $this->assertNull($report->totalStoppedTimeHours);
        $this->assertNull($report->totalFuelUsedGallons);
        $this->assertNull($report->averageMilesPerDay);
        $this->assertNull($report->averageSpeed);
        $this->assertNull($report->utilizationPercentage);
        $this->assertEmpty($report->dailyUtilization);
    }
}

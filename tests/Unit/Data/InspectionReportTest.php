<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Enums\InspectionType;
use Motive\Data\InspectionDefect;
use Motive\Data\InspectionReport;
use Motive\Enums\InspectionStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionReportTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $report = InspectionReport::from([
            'id'              => 123,
            'driver_id'       => 456,
            'vehicle_id'      => 789,
            'inspection_type' => 'pre_trip',
            'status'          => 'passed',
            'started_at'      => '2024-01-15T08:00:00Z',
        ]);

        $array = $report->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['driver_id']);
        $this->assertSame(789, $array['vehicle_id']);
        $this->assertSame('pre_trip', $array['inspection_type']);
        $this->assertSame('passed', $array['status']);
        $this->assertArrayHasKey('started_at', $array);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $report = InspectionReport::from([
            'id'              => 123,
            'driver_id'       => 456,
            'vehicle_id'      => 789,
            'inspection_type' => 'pre_trip',
            'status'          => 'passed',
            'odometer'        => 125000.5,
            'location'        => 'San Francisco, CA',
            'started_at'      => '2024-01-15T08:00:00Z',
            'completed_at'    => '2024-01-15T08:30:00Z',
            'signature'       => 'John Doe',
            'notes'           => 'All systems nominal',
            'created_at'      => '2024-01-15T08:30:00Z',
        ]);

        $this->assertSame(123, $report->id);
        $this->assertSame(456, $report->driverId);
        $this->assertSame(789, $report->vehicleId);
        $this->assertSame(InspectionType::PreTrip, $report->inspectionType);
        $this->assertSame(InspectionStatus::Passed, $report->status);
        $this->assertSame(125000.5, $report->odometer);
        $this->assertSame('San Francisco, CA', $report->location);
        $this->assertInstanceOf(CarbonImmutable::class, $report->startedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $report->completedAt);
        $this->assertSame('John Doe', $report->signature);
        $this->assertSame('All systems nominal', $report->notes);
        $this->assertInstanceOf(CarbonImmutable::class, $report->createdAt);
    }

    #[Test]
    public function it_handles_failed_inspection_with_defects(): void
    {
        $report = InspectionReport::from([
            'id'              => 123,
            'driver_id'       => 456,
            'vehicle_id'      => 789,
            'inspection_type' => 'post_trip',
            'status'          => 'failed',
            'started_at'      => '2024-01-15T16:00:00Z',
            'defects'         => [
                [
                    'id'            => 1,
                    'inspection_id' => 123,
                    'category'      => 'brakes',
                    'description'   => 'Worn brake pads',
                ],
                [
                    'id'            => 2,
                    'inspection_id' => 123,
                    'category'      => 'lights',
                    'description'   => 'Broken tail light',
                ],
            ],
        ]);

        $this->assertSame(InspectionStatus::Failed, $report->status);
        $this->assertCount(2, $report->defects);
        $this->assertInstanceOf(InspectionDefect::class, $report->defects[0]);
        $this->assertSame('brakes', $report->defects[0]->category);
        $this->assertInstanceOf(InspectionDefect::class, $report->defects[1]);
        $this->assertSame('lights', $report->defects[1]->category);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $report = InspectionReport::from([
            'id'              => 123,
            'driver_id'       => 456,
            'inspection_type' => 'dot',
            'started_at'      => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $report->id);
        $this->assertNull($report->vehicleId);
        $this->assertNull($report->status);
        $this->assertNull($report->odometer);
        $this->assertNull($report->location);
        $this->assertNull($report->completedAt);
        $this->assertNull($report->signature);
        $this->assertNull($report->notes);
        $this->assertEmpty($report->defects);
    }
}

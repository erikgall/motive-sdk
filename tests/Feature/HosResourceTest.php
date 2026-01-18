<?php

namespace Motive\Tests\Feature;

use Motive\Data\HosLog;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use Motive\Enums\DutyStatus;
use Motive\Data\HosViolation;
use Motive\Data\HosAvailability;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\HoursOfService\HosLogsResource;
use Motive\Resources\HoursOfService\HosViolationsResource;
use Motive\Resources\HoursOfService\HosAvailabilityResource;

/**
 * Feature tests for HOS Resources integration.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_certifies_hos_log_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_logs/certify' => Http::response([], 200),
        ]);

        $result = Motive::hosLogs()->certify(1, '2024-01-15');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/hos_logs/certify')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_creates_hos_log_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_logs' => Http::response([
                'hos_log' => [
                    'id'          => 1,
                    'driver_id'   => 100,
                    'duty_status' => 'driving',
                    'start_time'  => '2024-01-15T08:00:00Z',
                    'duration'    => 3600,
                    'vehicle_id'  => 50,
                    'location'    => 'San Francisco, CA',
                ],
            ], 201),
        ]);

        $log = Motive::hosLogs()->create([
            'driver_id'   => 100,
            'duty_status' => 'driving',
            'start_time'  => '2024-01-15T08:00:00Z',
        ]);

        $this->assertInstanceOf(HosLog::class, $log);
        $this->assertEquals(1, $log->id);
        $this->assertEquals(DutyStatus::Driving, $log->dutyStatus);
    }

    #[Test]
    public function it_finds_hos_log_by_id_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_logs/1' => Http::response([
                'hos_log' => [
                    'id'          => 1,
                    'driver_id'   => 100,
                    'duty_status' => 'off_duty',
                    'start_time'  => '2024-01-15T17:00:00Z',
                    'duration'    => 28800,
                ],
            ], 200),
        ]);

        $log = Motive::hosLogs()->find(1);

        $this->assertInstanceOf(HosLog::class, $log);
        $this->assertEquals(DutyStatus::OffDuty, $log->dutyStatus);
    }

    #[Test]
    public function it_gets_driver_availability_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_availability/driver/100' => Http::response([
                'hos_availability' => [
                    'driver_id'            => 100,
                    'drive_time_remaining' => 39600,
                    'shift_time_remaining' => 50400,
                    'cycle_time_remaining' => 252000,
                    'break_time_required'  => false,
                ],
            ], 200),
        ]);

        $availability = Motive::hosAvailability()->forDriver(100);

        $this->assertInstanceOf(HosAvailability::class, $availability);
        $this->assertEquals(100, $availability->driverId);
        $this->assertEquals(39600, $availability->driveTimeRemaining);
    }

    #[Test]
    public function it_gets_hos_availability_resource_from_manager(): void
    {
        $resource = Motive::hosAvailability();

        $this->assertInstanceOf(HosAvailabilityResource::class, $resource);
    }

    #[Test]
    public function it_gets_hos_logs_resource_from_manager(): void
    {
        $resource = Motive::hosLogs();

        $this->assertInstanceOf(HosLogsResource::class, $resource);
    }

    #[Test]
    public function it_gets_hos_violations_resource_from_manager(): void
    {
        $resource = Motive::hosViolations();

        $this->assertInstanceOf(HosViolationsResource::class, $resource);
    }

    #[Test]
    public function it_lists_hos_availability_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_availability*' => Http::response([
                'hos_availabilities' => [
                    [
                        'driver_id'            => 100,
                        'drive_time_remaining' => 39600,
                        'shift_time_remaining' => 50400,
                        'cycle_time_remaining' => 252000,
                    ],
                    [
                        'driver_id'            => 101,
                        'drive_time_remaining' => 36000,
                        'shift_time_remaining' => 43200,
                        'cycle_time_remaining' => 230400,
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 2,
                ],
            ], 200),
        ]);

        $availability = Motive::hosAvailability()->list();

        $this->assertCount(2, iterator_to_array($availability));
    }

    #[Test]
    public function it_lists_hos_logs_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_logs*' => Http::response([
                'hos_logs' => [
                    [
                        'id'          => 1,
                        'driver_id'   => 100,
                        'duty_status' => 'driving',
                        'start_time'  => '2024-01-15T08:00:00Z',
                        'duration'    => 3600,
                    ],
                    [
                        'id'          => 2,
                        'driver_id'   => 100,
                        'duty_status' => 'on_duty',
                        'start_time'  => '2024-01-15T09:00:00Z',
                        'duration'    => 1800,
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 2,
                ],
            ], 200),
        ]);

        $logs = Motive::hosLogs()->list();

        $this->assertCount(2, iterator_to_array($logs));
    }

    #[Test]
    public function it_lists_hos_violations_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_violations*' => Http::response([
                'hos_violations' => [
                    [
                        'id'             => 1,
                        'driver_id'      => 100,
                        'violation_type' => 'drive_time',
                        'start_time'     => '2024-01-15T18:00:00Z',
                        'duration'       => 600,
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 1,
                ],
            ], 200),
        ]);

        $violations = Motive::hosViolations()->list();
        $violationsArray = iterator_to_array($violations);

        $this->assertCount(1, $violationsArray);
        $this->assertInstanceOf(HosViolation::class, $violationsArray[0]);
    }

    #[Test]
    public function it_updates_hos_log_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/hos_logs/1' => Http::response([
                'hos_log' => [
                    'id'          => 1,
                    'driver_id'   => 100,
                    'duty_status' => 'driving',
                    'start_time'  => '2024-01-15T08:00:00Z',
                    'duration'    => 3600,
                    'annotation'  => 'Updated log entry',
                ],
            ], 200),
        ]);

        $log = Motive::hosLogs()->update(1, ['annotation' => 'Updated log entry']);

        $this->assertInstanceOf(HosLog::class, $log);
        $this->assertEquals('Updated log entry', $log->annotation);
    }
}

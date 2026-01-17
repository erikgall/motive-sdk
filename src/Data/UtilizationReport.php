<?php

namespace Motive\Data;

/**
 * Utilization report data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class UtilizationReport extends DataTransferObject
{
    /**
     * @param  array<int, UtilizationDay>  $dailyUtilization
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public ?int $vehicleId = null,
        public ?float $totalMiles = null,
        public ?float $totalDrivingTimeHours = null,
        public ?float $totalIdleTimeHours = null,
        public ?float $totalStoppedTimeHours = null,
        public ?float $totalFuelUsedGallons = null,
        public ?float $averageMilesPerDay = null,
        public ?float $averageSpeed = null,
        public ?float $utilizationPercentage = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public array $dailyUtilization = []
    ) {}

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [
            'dailyUtilization' => UtilizationDay::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'               => 'companyId',
            'vehicle_id'               => 'vehicleId',
            'total_miles'              => 'totalMiles',
            'total_driving_time_hours' => 'totalDrivingTimeHours',
            'total_idle_time_hours'    => 'totalIdleTimeHours',
            'total_stopped_time_hours' => 'totalStoppedTimeHours',
            'total_fuel_used_gallons'  => 'totalFuelUsedGallons',
            'average_miles_per_day'    => 'averageMilesPerDay',
            'average_speed'            => 'averageSpeed',
            'utilization_percentage'   => 'utilizationPercentage',
            'start_date'               => 'startDate',
            'end_date'                 => 'endDate',
            'daily_utilization'        => 'dailyUtilization',
        ];
    }
}

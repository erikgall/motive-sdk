<?php

namespace Motive\Data;

/**
 * Utilization day data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class UtilizationDay extends DataTransferObject
{
    public function __construct(
        public string $date,
        public float $totalMiles,
        public ?float $drivingTimeHours = null,
        public ?float $idleTimeHours = null,
        public ?float $stoppedTimeHours = null,
        public ?float $fuelUsedGallons = null,
        public ?float $averageSpeed = null,
        public ?float $maxSpeed = null
    ) {}

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'total_miles'        => 'totalMiles',
            'driving_time_hours' => 'drivingTimeHours',
            'idle_time_hours'    => 'idleTimeHours',
            'stopped_time_hours' => 'stoppedTimeHours',
            'fuel_used_gallons'  => 'fuelUsedGallons',
            'average_speed'      => 'averageSpeed',
            'max_speed'          => 'maxSpeed',
        ];
    }
}

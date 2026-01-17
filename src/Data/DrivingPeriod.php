<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Driving period data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DrivingPeriod extends DataTransferObject
{
    /**
     * @param  array<string, mixed>|null  $startLocation
     * @param  array<string, mixed>|null  $endLocation
     */
    public function __construct(
        public int $id,
        public int $driverId,
        public CarbonImmutable $startTime,
        public ?int $vehicleId = null,
        public ?CarbonImmutable $endTime = null,
        public ?float $distance = null,
        public ?float $averageSpeed = null,
        public ?float $maxSpeed = null,
        public ?array $startLocation = null,
        public ?array $endLocation = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['startTime', 'endTime'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'driver_id'      => 'driverId',
            'vehicle_id'     => 'vehicleId',
            'start_time'     => 'startTime',
            'end_time'       => 'endTime',
            'average_speed'  => 'averageSpeed',
            'max_speed'      => 'maxSpeed',
            'start_location' => 'startLocation',
            'end_location'   => 'endLocation',
        ];
    }
}

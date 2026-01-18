<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Driving period data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $driverId
 * @property CarbonImmutable $startTime
 * @property int|null $vehicleId
 * @property CarbonImmutable|null $endTime
 * @property float|null $distance
 * @property float|null $averageSpeed
 * @property float|null $maxSpeed
 * @property array<string, mixed>|null $startLocation
 * @property array<string, mixed>|null $endLocation
 */
class DrivingPeriod extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'           => 'int',
        'driverId'     => 'int',
        'vehicleId'    => 'int',
        'distance'     => 'float',
        'averageSpeed' => 'float',
        'maxSpeed'     => 'float',
        'startTime'    => CarbonImmutable::class,
        'endTime'      => CarbonImmutable::class,
    ];
}

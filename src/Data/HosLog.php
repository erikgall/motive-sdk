<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\DutyStatus;

/**
 * Hours of Service log entry data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $driverId
 * @property DutyStatus $dutyStatus
 * @property CarbonImmutable $startTime
 * @property int|null $vehicleId
 * @property CarbonImmutable|null $endTime
 * @property int|null $duration
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $location
 * @property float|null $odometer
 * @property string|null $annotation
 * @property bool|null $certified
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class HosLog extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'driverId'   => 'int',
        'vehicleId'  => 'int',
        'duration'   => 'int',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'odometer'   => 'float',
        'certified'  => 'bool',
        'dutyStatus' => DutyStatus::class,
        'startTime'  => CarbonImmutable::class,
        'endTime'    => CarbonImmutable::class,
        'createdAt'  => CarbonImmutable::class,
        'updatedAt'  => CarbonImmutable::class,
    ];
}

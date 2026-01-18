<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\EventSeverity;
use Motive\Enums\PerformanceEventType;

/**
 * Driver performance event data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property int $driverId
 * @property PerformanceEventType $eventType
 * @property EventSeverity $severity
 * @property int|null $vehicleId
 * @property int|null $duration
 * @property float|null $speed
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $address
 * @property string|null $videoUrl
 * @property CarbonImmutable|null $occurredAt
 * @property CarbonImmutable|null $createdAt
 */
class DriverPerformanceEvent extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'companyId'  => 'int',
        'driverId'   => 'int',
        'vehicleId'  => 'int',
        'duration'   => 'int',
        'speed'      => 'float',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'eventType'  => PerformanceEventType::class,
        'severity'   => EventSeverity::class,
        'occurredAt' => CarbonImmutable::class,
        'createdAt'  => CarbonImmutable::class,
    ];
}

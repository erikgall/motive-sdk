<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\HosViolationType;

/**
 * Hours of Service violation data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $driverId
 * @property HosViolationType $violationType
 * @property CarbonImmutable $startTime
 * @property int|null $vehicleId
 * @property CarbonImmutable|null $endTime
 * @property int|null $duration
 * @property string|null $severity
 * @property string|null $location
 * @property CarbonImmutable|null $createdAt
 */
class HosViolation extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'            => 'int',
        'driverId'      => 'int',
        'vehicleId'     => 'int',
        'duration'      => 'int',
        'violationType' => HosViolationType::class,
        'startTime'     => CarbonImmutable::class,
        'endTime'       => CarbonImmutable::class,
        'createdAt'     => CarbonImmutable::class,
    ];
}

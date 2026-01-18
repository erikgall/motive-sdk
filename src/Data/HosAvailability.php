<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Hours of Service availability data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $driverId
 * @property int|null $driveTimeRemaining
 * @property int|null $shiftTimeRemaining
 * @property int|null $cycleTimeRemaining
 * @property int|null $breakTimeRequired
 * @property int|null $cycleTomorrow
 * @property int|null $recap
 * @property CarbonImmutable|null $lastCalculatedAt
 */
class HosAvailability extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'driverId'           => 'int',
        'driveTimeRemaining' => 'int',
        'shiftTimeRemaining' => 'int',
        'cycleTimeRemaining' => 'int',
        'breakTimeRequired'  => 'int',
        'cycleTomorrow'      => 'int',
        'recap'              => 'int',
        'lastCalculatedAt'   => CarbonImmutable::class,
    ];
}

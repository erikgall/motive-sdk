<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Timecard entry data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $timecardId
 * @property string $entryType
 * @property CarbonImmutable|null $startTime
 * @property CarbonImmutable|null $endTime
 * @property int|null $duration
 * @property string|null $notes
 */
class TimecardEntry extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'timecardId' => 'int',
        'duration'   => 'int',
        'startTime'  => CarbonImmutable::class,
        'endTime'    => CarbonImmutable::class,
    ];
}

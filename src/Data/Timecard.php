<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\TimecardStatus;

/**
 * Timecard data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property int $driverId
 * @property string $date
 * @property TimecardStatus $status
 * @property float|null $totalHours
 * @property float|null $regularHours
 * @property float|null $overtimeHours
 * @property int|null $breakTime
 * @property int|null $approvedById
 * @property CarbonImmutable|null $approvedAt
 * @property array<int, TimecardEntry> $entries
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Timecard extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'            => 'int',
        'companyId'     => 'int',
        'driverId'      => 'int',
        'breakTime'     => 'int',
        'approvedById'  => 'int',
        'totalHours'    => 'float',
        'regularHours'  => 'float',
        'overtimeHours' => 'float',
        'status'        => TimecardStatus::class,
        'approvedAt'    => CarbonImmutable::class,
        'createdAt'     => CarbonImmutable::class,
        'updatedAt'     => CarbonImmutable::class,
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'entries' => TimecardEntry::class,
    ];
}

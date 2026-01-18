<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\DispatchStatus;

/**
 * Dispatch data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property DispatchStatus $status
 * @property int|null $driverId
 * @property int|null $vehicleId
 * @property string|null $externalId
 * @property string|null $reference
 * @property string|null $notes
 * @property CarbonImmutable|null $startedAt
 * @property CarbonImmutable|null $completedAt
 * @property array<int, DispatchStop> $stops
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Dispatch extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'          => 'int',
        'companyId'   => 'int',
        'driverId'    => 'int',
        'vehicleId'   => 'int',
        'status'      => DispatchStatus::class,
        'startedAt'   => CarbonImmutable::class,
        'completedAt' => CarbonImmutable::class,
        'createdAt'   => CarbonImmutable::class,
        'updatedAt'   => CarbonImmutable::class,
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'stops' => DispatchStop::class,
    ];
}

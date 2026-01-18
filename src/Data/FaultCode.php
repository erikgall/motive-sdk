<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Vehicle fault code data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $vehicleId
 * @property string $code
 * @property string|null $description
 * @property string|null $source
 * @property int|null $spn
 * @property int|null $fmi
 * @property int|null $occurrenceCount
 * @property CarbonImmutable|null $firstOccurrenceAt
 * @property CarbonImmutable|null $lastOccurrenceAt
 * @property bool|null $resolved
 * @property CarbonImmutable|null $resolvedAt
 * @property CarbonImmutable|null $createdAt
 */
class FaultCode extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'                => 'int',
        'vehicleId'         => 'int',
        'spn'               => 'int',
        'fmi'               => 'int',
        'occurrenceCount'   => 'int',
        'resolved'          => 'bool',
        'firstOccurrenceAt' => CarbonImmutable::class,
        'lastOccurrenceAt'  => CarbonImmutable::class,
        'resolvedAt'        => CarbonImmutable::class,
        'createdAt'         => CarbonImmutable::class,
    ];
}

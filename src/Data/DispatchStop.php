<?php

namespace Motive\Data;

use Motive\Enums\StopType;
use Carbon\CarbonImmutable;

/**
 * Dispatch stop data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $dispatchId
 * @property StopType $stopType
 * @property string|null $name
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postalCode
 * @property string|null $country
 * @property float|null $latitude
 * @property float|null $longitude
 * @property CarbonImmutable|null $scheduledAt
 * @property CarbonImmutable|null $arrivedAt
 * @property CarbonImmutable|null $departedAt
 * @property string|null $notes
 * @property int|null $sequence
 * @property CarbonImmutable|null $createdAt
 */
class DispatchStop extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'          => 'int',
        'dispatchId'  => 'int',
        'latitude'    => 'float',
        'longitude'   => 'float',
        'sequence'    => 'int',
        'stopType'    => StopType::class,
        'scheduledAt' => CarbonImmutable::class,
        'arrivedAt'   => CarbonImmutable::class,
        'departedAt'  => CarbonImmutable::class,
        'createdAt'   => CarbonImmutable::class,
    ];
}

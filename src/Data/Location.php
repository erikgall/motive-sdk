<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Location data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $name
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postalCode
 * @property string|null $country
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $externalId
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Location extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'companyId' => 'int',
        'latitude'  => 'float',
        'longitude' => 'float',
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];
}

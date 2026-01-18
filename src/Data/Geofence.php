<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\GeofenceType;

/**
 * Geofence data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $name
 * @property GeofenceType $geofenceType
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $radius
 * @property string|null $externalId
 * @property array<int, GeofenceCoordinate> $coordinates
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Geofence extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'           => 'int',
        'companyId'    => 'int',
        'latitude'     => 'float',
        'longitude'    => 'float',
        'radius'       => 'int',
        'geofenceType' => GeofenceType::class,
        'createdAt'    => CarbonImmutable::class,
        'updatedAt'    => CarbonImmutable::class,
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'coordinates' => GeofenceCoordinate::class,
    ];
}

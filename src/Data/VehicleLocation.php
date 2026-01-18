<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Vehicle location data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property float $latitude
 * @property float $longitude
 * @property float|null $speed
 * @property int|null $bearing
 * @property string|null $address
 * @property CarbonImmutable|null $locatedAt
 */
class VehicleLocation extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'speed'     => 'float',
        'bearing'   => 'int',
        'locatedAt' => CarbonImmutable::class,
    ];
}

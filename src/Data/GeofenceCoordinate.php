<?php

namespace Motive\Data;

/**
 * Geofence coordinate data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property float $latitude
 * @property float $longitude
 * @property int|null $sequence
 */
class GeofenceCoordinate extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'sequence'  => 'int',
    ];
}

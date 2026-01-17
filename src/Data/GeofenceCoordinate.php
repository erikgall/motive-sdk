<?php

namespace Motive\Data;

/**
 * Geofence coordinate data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofenceCoordinate extends DataTransferObject
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?int $sequence = null
    ) {}
}

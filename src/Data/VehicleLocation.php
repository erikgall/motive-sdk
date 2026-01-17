<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Vehicle location data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleLocation extends DataTransferObject
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?float $speed = null,
        public ?int $bearing = null,
        public ?string $address = null,
        public ?CarbonImmutable $locatedAt = null
    ) {}
}

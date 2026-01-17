<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Shipment tracking data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ShipmentTracking extends DataTransferObject
{
    /**
     * @param  array<string, mixed>|null  $currentLocation
     */
    public function __construct(
        public int $id,
        public int $shipmentId,
        public ?array $currentLocation = null,
        public ?CarbonImmutable $lastUpdate = null,
        public ?float $speed = null,
        public ?int $heading = null,
        public ?float $distanceRemaining = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['lastUpdate'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'shipment_id'        => 'shipmentId',
            'current_location'   => 'currentLocation',
            'last_update'        => 'lastUpdate',
            'distance_remaining' => 'distanceRemaining',
        ];
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Shipment ETA data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ShipmentEta extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $shipmentId,
        public ?CarbonImmutable $estimatedArrival = null,
        public ?float $distanceRemaining = null,
        public ?int $timeRemaining = null,
        public ?float $confidence = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['estimatedArrival'];
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
            'estimated_arrival'  => 'estimatedArrival',
            'distance_remaining' => 'distanceRemaining',
            'time_remaining'     => 'timeRemaining',
        ];
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\ShipmentStatus;

/**
 * Shipment data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Shipment extends DataTransferObject
{
    public function __construct(
        public int $id,
        public ShipmentStatus $status,
        public ?string $referenceId = null,
        public ?string $origin = null,
        public ?string $destination = null,
        public ?int $driverId = null,
        public ?int $vehicleId = null,
        public ?CarbonImmutable $estimatedArrival = null,
        public ?CarbonImmutable $actualArrival = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['estimatedArrival', 'actualArrival', 'createdAt', 'updatedAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'status' => ShipmentStatus::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'reference_id'      => 'referenceId',
            'driver_id'         => 'driverId',
            'vehicle_id'        => 'vehicleId',
            'estimated_arrival' => 'estimatedArrival',
            'actual_arrival'    => 'actualArrival',
            'created_at'        => 'createdAt',
            'updated_at'        => 'updatedAt',
        ];
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Vehicle gateway data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleGateway extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $vehicleId,
        public string $serialNumber,
        public ?string $firmwareVersion = null,
        public bool $connected = false,
        public ?CarbonImmutable $lastSeenAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['lastSeenAt', 'createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'vehicle_id'       => 'vehicleId',
            'serial_number'    => 'serialNumber',
            'firmware_version' => 'firmwareVersion',
            'last_seen_at'     => 'lastSeenAt',
            'created_at'       => 'createdAt',
        ];
    }
}

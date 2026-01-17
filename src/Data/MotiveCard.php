<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Motive card data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveCard extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $cardNumber,
        public ?int $driverId = null,
        public ?int $vehicleId = null,
        public bool $active = true,
        public ?CarbonImmutable $expiresAt = null,
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
        return ['expiresAt', 'createdAt', 'updatedAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'card_number' => 'cardNumber',
            'driver_id'   => 'driverId',
            'vehicle_id'  => 'vehicleId',
            'expires_at'  => 'expiresAt',
            'created_at'  => 'createdAt',
            'updated_at'  => 'updatedAt',
        ];
    }
}

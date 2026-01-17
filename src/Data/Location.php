<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Location data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Location extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public string $name,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $externalId = null,
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
        return ['createdAt', 'updatedAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'  => 'companyId',
            'postal_code' => 'postalCode',
            'external_id' => 'externalId',
            'created_at'  => 'createdAt',
            'updated_at'  => 'updatedAt',
        ];
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * External ID mapping data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ExternalId extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $resourceType,
        public int $resourceId,
        public string $externalId,
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
            'resource_type' => 'resourceType',
            'resource_id'   => 'resourceId',
            'external_id'   => 'externalId',
            'created_at'    => 'createdAt',
            'updated_at'    => 'updatedAt',
        ];
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Group data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Group extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public string $name,
        public ?string $description = null,
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
            'external_id' => 'externalId',
            'created_at'  => 'createdAt',
            'updated_at'  => 'updatedAt',
        ];
    }
}

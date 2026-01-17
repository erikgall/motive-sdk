<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Form data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Form extends DataTransferObject
{
    /**
     * @param  array<int, FormField>  $fields
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public string $name,
        public ?string $description = null,
        public bool $active = true,
        public array $fields = [],
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
     * Properties that should be cast to arrays of DTOs.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [
            'fields' => FormField::class,
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
            'company_id' => 'companyId',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];
    }
}

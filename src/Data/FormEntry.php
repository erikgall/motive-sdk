<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Form entry data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormEntry extends DataTransferObject
{
    /**
     * @param  array<int, array<string, mixed>>  $fieldValues
     * @param  array<string, mixed>|null  $location
     */
    public function __construct(
        public int $id,
        public int $formId,
        public int $driverId,
        public ?int $vehicleId = null,
        public ?CarbonImmutable $submittedAt = null,
        public array $fieldValues = [],
        public ?array $location = null,
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
        return ['submittedAt', 'createdAt', 'updatedAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'form_id'      => 'formId',
            'driver_id'    => 'driverId',
            'vehicle_id'   => 'vehicleId',
            'submitted_at' => 'submittedAt',
            'field_values' => 'fieldValues',
            'created_at'   => 'createdAt',
            'updated_at'   => 'updatedAt',
        ];
    }
}

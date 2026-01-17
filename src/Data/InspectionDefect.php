<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Driver Vehicle Inspection Report (DVIR) defect data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionDefect extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $inspectionId,
        public string $category,
        public string $description,
        public ?string $severity = null,
        public ?bool $corrected = null,
        public ?CarbonImmutable $correctedAt = null,
        public ?int $correctedById = null,
        public ?string $notes = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['correctedAt', 'createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'inspection_id'   => 'inspectionId',
            'corrected_at'    => 'correctedAt',
            'corrected_by_id' => 'correctedById',
            'created_at'      => 'createdAt',
        ];
    }
}

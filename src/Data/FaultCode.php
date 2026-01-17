<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Vehicle fault code data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FaultCode extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $vehicleId,
        public string $code,
        public ?string $description = null,
        public ?string $source = null,
        public ?int $spn = null,
        public ?int $fmi = null,
        public ?int $occurrenceCount = null,
        public ?CarbonImmutable $firstOccurrenceAt = null,
        public ?CarbonImmutable $lastOccurrenceAt = null,
        public ?bool $resolved = null,
        public ?CarbonImmutable $resolvedAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['firstOccurrenceAt', 'lastOccurrenceAt', 'resolvedAt', 'createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'vehicle_id'          => 'vehicleId',
            'occurrence_count'    => 'occurrenceCount',
            'first_occurrence_at' => 'firstOccurrenceAt',
            'last_occurrence_at'  => 'lastOccurrenceAt',
            'resolved_at'         => 'resolvedAt',
            'created_at'          => 'createdAt',
        ];
    }
}

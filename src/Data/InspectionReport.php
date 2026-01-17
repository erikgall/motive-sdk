<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\InspectionType;
use Motive\Enums\InspectionStatus;

/**
 * Driver Vehicle Inspection Report (DVIR) data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionReport extends DataTransferObject
{
    /**
     * @param  array<int, InspectionDefect>  $defects
     */
    public function __construct(
        public int $id,
        public int $driverId,
        public InspectionType $inspectionType,
        public CarbonImmutable $startedAt,
        public ?int $vehicleId = null,
        public ?InspectionStatus $status = null,
        public ?float $odometer = null,
        public ?string $location = null,
        public ?CarbonImmutable $completedAt = null,
        public ?string $signature = null,
        public ?string $notes = null,
        public array $defects = [],
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['startedAt', 'completedAt', 'createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'inspectionType' => InspectionType::class,
            'status'         => InspectionStatus::class,
        ];
    }

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [
            'defects' => InspectionDefect::class,
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
            'driver_id'       => 'driverId',
            'vehicle_id'      => 'vehicleId',
            'inspection_type' => 'inspectionType',
            'started_at'      => 'startedAt',
            'completed_at'    => 'completedAt',
            'created_at'      => 'createdAt',
        ];
    }
}

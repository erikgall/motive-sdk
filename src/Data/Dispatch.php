<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\DispatchStatus;

/**
 * Dispatch data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Dispatch extends DataTransferObject
{
    /**
     * @param  array<int, DispatchStop>  $stops
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public DispatchStatus $status,
        public ?int $driverId = null,
        public ?int $vehicleId = null,
        public ?string $externalId = null,
        public ?string $reference = null,
        public ?string $notes = null,
        public ?CarbonImmutable $startedAt = null,
        public ?CarbonImmutable $completedAt = null,
        public array $stops = [],
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
        return ['startedAt', 'completedAt', 'createdAt', 'updatedAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'status' => DispatchStatus::class,
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
            'stops' => DispatchStop::class,
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
            'company_id'   => 'companyId',
            'driver_id'    => 'driverId',
            'vehicle_id'   => 'vehicleId',
            'external_id'  => 'externalId',
            'started_at'   => 'startedAt',
            'completed_at' => 'completedAt',
            'created_at'   => 'createdAt',
            'updated_at'   => 'updatedAt',
        ];
    }
}

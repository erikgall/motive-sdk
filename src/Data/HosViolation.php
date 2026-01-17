<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\HosViolationType;

/**
 * Hours of Service violation data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosViolation extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $driverId,
        public HosViolationType $violationType,
        public CarbonImmutable $startTime,
        public ?int $vehicleId = null,
        public ?CarbonImmutable $endTime = null,
        public ?int $duration = null,
        public ?string $severity = null,
        public ?string $location = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'driver_id'      => 'driverId',
            'vehicle_id'     => 'vehicleId',
            'violation_type' => 'violationType',
            'start_time'     => 'startTime',
            'end_time'       => 'endTime',
            'created_at'     => 'createdAt',
        ];
    }

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['startTime', 'endTime', 'createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'violationType' => HosViolationType::class,
        ];
    }
}

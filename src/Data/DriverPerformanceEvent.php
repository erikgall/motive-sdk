<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\EventSeverity;
use Motive\Enums\PerformanceEventType;

/**
 * Driver performance event data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DriverPerformanceEvent extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public int $driverId,
        public PerformanceEventType $eventType,
        public EventSeverity $severity,
        public ?int $vehicleId = null,
        public ?int $duration = null,
        public ?float $speed = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $address = null,
        public ?string $videoUrl = null,
        public ?CarbonImmutable $occurredAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['occurredAt', 'createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'eventType' => PerformanceEventType::class,
            'severity'  => EventSeverity::class,
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
            'company_id'  => 'companyId',
            'driver_id'   => 'driverId',
            'vehicle_id'  => 'vehicleId',
            'event_type'  => 'eventType',
            'video_url'   => 'videoUrl',
            'occurred_at' => 'occurredAt',
            'created_at'  => 'createdAt',
        ];
    }
}

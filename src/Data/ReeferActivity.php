<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Reefer activity data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ReeferActivity extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $vehicleId,
        public ?float $temperature = null,
        public ?float $setpoint = null,
        public ?string $mode = null,
        public ?float $fuelLevel = null,
        public bool $engineRunning = false,
        public ?CarbonImmutable $recordedAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['recordedAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'vehicle_id'     => 'vehicleId',
            'fuel_level'     => 'fuelLevel',
            'engine_running' => 'engineRunning',
            'recorded_at'    => 'recordedAt',
        ];
    }
}

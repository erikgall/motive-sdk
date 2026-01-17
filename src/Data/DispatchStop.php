<?php

namespace Motive\Data;

use Motive\Enums\StopType;
use Carbon\CarbonImmutable;

/**
 * Dispatch stop data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchStop extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $dispatchId,
        public StopType $stopType,
        public ?string $name = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?CarbonImmutable $scheduledAt = null,
        public ?CarbonImmutable $arrivedAt = null,
        public ?CarbonImmutable $departedAt = null,
        public ?string $notes = null,
        public ?int $sequence = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['scheduledAt', 'arrivedAt', 'departedAt', 'createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'stopType' => StopType::class,
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
            'dispatch_id'  => 'dispatchId',
            'stop_type'    => 'stopType',
            'postal_code'  => 'postalCode',
            'scheduled_at' => 'scheduledAt',
            'arrived_at'   => 'arrivedAt',
            'departed_at'  => 'departedAt',
            'created_at'   => 'createdAt',
        ];
    }
}

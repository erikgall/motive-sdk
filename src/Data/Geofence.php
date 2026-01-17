<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\GeofenceType;

/**
 * Geofence data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Geofence extends DataTransferObject
{
    /**
     * @param  array<int, GeofenceCoordinate>  $coordinates
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public string $name,
        public GeofenceType $geofenceType,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?int $radius = null,
        public ?string $externalId = null,
        public array $coordinates = [],
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
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'geofenceType' => GeofenceType::class,
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
            'coordinates' => GeofenceCoordinate::class,
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
            'company_id'    => 'companyId',
            'geofence_type' => 'geofenceType',
            'external_id'   => 'externalId',
            'created_at'    => 'createdAt',
            'updated_at'    => 'updatedAt',
        ];
    }
}

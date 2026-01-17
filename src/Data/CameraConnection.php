<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\CameraType;

/**
 * Camera connection data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class CameraConnection extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $vehicleId,
        public CameraType $cameraType,
        public ?string $serialNumber = null,
        public bool $connected = false,
        public ?CarbonImmutable $lastSeenAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['lastSeenAt', 'createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'cameraType' => CameraType::class,
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
            'vehicle_id'    => 'vehicleId',
            'camera_type'   => 'cameraType',
            'serial_number' => 'serialNumber',
            'last_seen_at'  => 'lastSeenAt',
            'created_at'    => 'createdAt',
        ];
    }
}

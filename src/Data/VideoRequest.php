<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\VideoStatus;

/**
 * Video request data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VideoRequest extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $vehicleId,
        public VideoStatus $status,
        public ?int $driverId = null,
        public ?string $videoUrl = null,
        public ?CarbonImmutable $startTime = null,
        public ?CarbonImmutable $endTime = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $completedAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['startTime', 'endTime', 'createdAt', 'completedAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'status' => VideoStatus::class,
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
            'vehicle_id'   => 'vehicleId',
            'driver_id'    => 'driverId',
            'video_url'    => 'videoUrl',
            'start_time'   => 'startTime',
            'end_time'     => 'endTime',
            'created_at'   => 'createdAt',
            'completed_at' => 'completedAt',
        ];
    }
}

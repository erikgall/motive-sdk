<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\VideoStatus;

/**
 * Video request data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $vehicleId
 * @property VideoStatus $status
 * @property int|null $driverId
 * @property string|null $videoUrl
 * @property CarbonImmutable|null $startTime
 * @property CarbonImmutable|null $endTime
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $completedAt
 */
class VideoRequest extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'          => 'int',
        'vehicleId'   => 'int',
        'driverId'    => 'int',
        'status'      => VideoStatus::class,
        'startTime'   => CarbonImmutable::class,
        'endTime'     => CarbonImmutable::class,
        'createdAt'   => CarbonImmutable::class,
        'completedAt' => CarbonImmutable::class,
    ];
}

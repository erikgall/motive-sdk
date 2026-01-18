<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\CameraType;

/**
 * Camera connection data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $vehicleId
 * @property CameraType $cameraType
 * @property string|null $serialNumber
 * @property bool $connected
 * @property CarbonImmutable|null $lastSeenAt
 * @property CarbonImmutable|null $createdAt
 */
class CameraConnection extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'vehicleId'  => 'int',
        'cameraType' => CameraType::class,
        'connected'  => 'bool',
        'lastSeenAt' => CarbonImmutable::class,
        'createdAt'  => CarbonImmutable::class,
    ];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'connected' => false,
    ];
}

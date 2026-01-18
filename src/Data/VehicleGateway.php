<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Vehicle gateway data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $vehicleId
 * @property string $serialNumber
 * @property string|null $firmwareVersion
 * @property bool $connected
 * @property CarbonImmutable|null $lastSeenAt
 * @property CarbonImmutable|null $createdAt
 */
class VehicleGateway extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'vehicleId'  => 'int',
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

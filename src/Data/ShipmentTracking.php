<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Shipment tracking data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $shipmentId
 * @property array<string, mixed>|null $currentLocation
 * @property CarbonImmutable|null $lastUpdate
 * @property float|null $speed
 * @property int|null $heading
 * @property float|null $distanceRemaining
 */
class ShipmentTracking extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'                => 'int',
        'shipmentId'        => 'int',
        'speed'             => 'float',
        'heading'           => 'int',
        'distanceRemaining' => 'float',
        'lastUpdate'        => CarbonImmutable::class,
    ];
}

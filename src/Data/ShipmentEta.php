<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Shipment ETA data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $shipmentId
 * @property CarbonImmutable|null $estimatedArrival
 * @property float|null $distanceRemaining
 * @property int|null $timeRemaining
 * @property float|null $confidence
 */
class ShipmentEta extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'                => 'int',
        'shipmentId'        => 'int',
        'distanceRemaining' => 'float',
        'timeRemaining'     => 'int',
        'confidence'        => 'float',
        'estimatedArrival'  => CarbonImmutable::class,
    ];
}

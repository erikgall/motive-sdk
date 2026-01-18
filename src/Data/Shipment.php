<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\ShipmentStatus;

/**
 * Shipment data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property ShipmentStatus $status
 * @property string|null $referenceId
 * @property string|null $origin
 * @property string|null $destination
 * @property int|null $driverId
 * @property int|null $vehicleId
 * @property CarbonImmutable|null $estimatedArrival
 * @property CarbonImmutable|null $actualArrival
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Shipment extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'               => 'int',
        'driverId'         => 'int',
        'vehicleId'        => 'int',
        'status'           => ShipmentStatus::class,
        'estimatedArrival' => CarbonImmutable::class,
        'actualArrival'    => CarbonImmutable::class,
        'createdAt'        => CarbonImmutable::class,
        'updatedAt'        => CarbonImmutable::class,
    ];
}

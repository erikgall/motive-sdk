<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Motive card data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $cardNumber
 * @property int|null $driverId
 * @property int|null $vehicleId
 * @property bool $active
 * @property CarbonImmutable|null $expiresAt
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class MotiveCard extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'driverId'  => 'int',
        'vehicleId' => 'int',
        'active'    => 'bool',
        'expiresAt' => CarbonImmutable::class,
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'active' => true,
    ];
}

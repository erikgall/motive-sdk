<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Fuel purchase data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $fuelType
 * @property float $quantity
 * @property float $totalCost
 * @property int|null $vehicleId
 * @property int|null $driverId
 * @property float|null $unitPrice
 * @property int|null $odometer
 * @property string|null $vendorName
 * @property string|null $vendorAddress
 * @property string|null $receiptNumber
 * @property CarbonImmutable|null $purchasedAt
 * @property CarbonImmutable|null $createdAt
 */
class FuelPurchase extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'          => 'int',
        'companyId'   => 'int',
        'vehicleId'   => 'int',
        'driverId'    => 'int',
        'odometer'    => 'int',
        'quantity'    => 'float',
        'totalCost'   => 'float',
        'unitPrice'   => 'float',
        'purchasedAt' => CarbonImmutable::class,
        'createdAt'   => CarbonImmutable::class,
    ];
}

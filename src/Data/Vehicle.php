<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\VehicleStatus;

/**
 * Vehicle data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $number
 * @property string|null $make
 * @property string|null $model
 * @property int|null $year
 * @property string|null $vin
 * @property VehicleStatus|null $status
 * @property string|null $licensePlateNumber
 * @property string|null $licensePlateState
 * @property int|null $currentDriverId
 * @property string|null $externalId
 * @property VehicleLocation|null $currentLocation
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Vehicle extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'              => 'int',
        'companyId'       => 'int',
        'year'            => 'int',
        'currentDriverId' => 'int',
        'status'          => VehicleStatus::class,
        'currentLocation' => VehicleLocation::class,
        'createdAt'       => CarbonImmutable::class,
        'updatedAt'       => CarbonImmutable::class,
    ];
}

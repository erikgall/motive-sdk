<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\AssetType;
use Motive\Enums\AssetStatus;

/**
 * Asset data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $name
 * @property int|null $companyId
 * @property AssetType|null $assetType
 * @property AssetStatus|null $status
 * @property string|null $serialNumber
 * @property string|null $make
 * @property string|null $model
 * @property int|null $year
 * @property string|null $licensePlateNumber
 * @property string|null $licensePlateState
 * @property string|null $vin
 * @property int|null $vehicleId
 * @property string|null $externalId
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Asset extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'companyId' => 'int',
        'year'      => 'int',
        'vehicleId' => 'int',
        'assetType' => AssetType::class,
        'status'    => AssetStatus::class,
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];
}

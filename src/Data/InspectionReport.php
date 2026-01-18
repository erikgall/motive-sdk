<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\InspectionType;
use Motive\Enums\InspectionStatus;

/**
 * Driver Vehicle Inspection Report (DVIR) data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $driverId
 * @property InspectionType $inspectionType
 * @property CarbonImmutable $startedAt
 * @property int|null $vehicleId
 * @property InspectionStatus|null $status
 * @property float|null $odometer
 * @property string|null $location
 * @property CarbonImmutable|null $completedAt
 * @property string|null $signature
 * @property string|null $notes
 * @property array<int, InspectionDefect> $defects
 * @property CarbonImmutable|null $createdAt
 */
class InspectionReport extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'             => 'int',
        'driverId'       => 'int',
        'vehicleId'      => 'int',
        'odometer'       => 'float',
        'inspectionType' => InspectionType::class,
        'status'         => InspectionStatus::class,
        'startedAt'      => CarbonImmutable::class,
        'completedAt'    => CarbonImmutable::class,
        'createdAt'      => CarbonImmutable::class,
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'defects' => InspectionDefect::class,
    ];
}

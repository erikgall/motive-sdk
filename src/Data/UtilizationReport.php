<?php

namespace Motive\Data;

/**
 * Utilization report data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property int|null $vehicleId
 * @property float|null $totalMiles
 * @property float|null $totalDrivingTimeHours
 * @property float|null $totalIdleTimeHours
 * @property float|null $totalStoppedTimeHours
 * @property float|null $totalFuelUsedGallons
 * @property float|null $averageMilesPerDay
 * @property float|null $averageSpeed
 * @property float|null $utilizationPercentage
 * @property string|null $startDate
 * @property string|null $endDate
 * @property array<int, UtilizationDay> $dailyUtilization
 */
class UtilizationReport extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'                    => 'int',
        'companyId'             => 'int',
        'vehicleId'             => 'int',
        'totalMiles'            => 'float',
        'totalDrivingTimeHours' => 'float',
        'totalIdleTimeHours'    => 'float',
        'totalStoppedTimeHours' => 'float',
        'totalFuelUsedGallons'  => 'float',
        'averageMilesPerDay'    => 'float',
        'averageSpeed'          => 'float',
        'utilizationPercentage' => 'float',
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'dailyUtilization' => UtilizationDay::class,
    ];
}

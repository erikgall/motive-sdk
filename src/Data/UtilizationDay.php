<?php

namespace Motive\Data;

/**
 * Utilization day data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property string $date
 * @property float $totalMiles
 * @property float|null $drivingTimeHours
 * @property float|null $idleTimeHours
 * @property float|null $stoppedTimeHours
 * @property float|null $fuelUsedGallons
 * @property float|null $averageSpeed
 * @property float|null $maxSpeed
 */
class UtilizationDay extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'totalMiles'       => 'float',
        'drivingTimeHours' => 'float',
        'idleTimeHours'    => 'float',
        'stoppedTimeHours' => 'float',
        'fuelUsedGallons'  => 'float',
        'averageSpeed'     => 'float',
        'maxSpeed'         => 'float',
    ];
}

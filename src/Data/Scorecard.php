<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Scorecard data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property float $overallScore
 * @property int|null $driverId
 * @property float|null $safetyScore
 * @property float|null $efficiencyScore
 * @property float|null $complianceScore
 * @property float|null $totalMiles
 * @property int|null $totalEvents
 * @property int|null $hardBrakingEvents
 * @property int|null $speedingEvents
 * @property int|null $rapidAccelEvents
 * @property int|null $idleTimeMinutes
 * @property CarbonImmutable|null $periodStart
 * @property CarbonImmutable|null $periodEnd
 * @property CarbonImmutable|null $createdAt
 */
class Scorecard extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'                => 'int',
        'companyId'         => 'int',
        'driverId'          => 'int',
        'totalEvents'       => 'int',
        'hardBrakingEvents' => 'int',
        'speedingEvents'    => 'int',
        'rapidAccelEvents'  => 'int',
        'idleTimeMinutes'   => 'int',
        'overallScore'      => 'float',
        'safetyScore'       => 'float',
        'efficiencyScore'   => 'float',
        'complianceScore'   => 'float',
        'totalMiles'        => 'float',
        'periodStart'       => CarbonImmutable::class,
        'periodEnd'         => CarbonImmutable::class,
        'createdAt'         => CarbonImmutable::class,
    ];
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Scorecard data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Scorecard extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public float $overallScore,
        public ?int $driverId = null,
        public ?float $safetyScore = null,
        public ?float $efficiencyScore = null,
        public ?float $complianceScore = null,
        public ?float $totalMiles = null,
        public ?int $totalEvents = null,
        public ?int $hardBrakingEvents = null,
        public ?int $speedingEvents = null,
        public ?int $rapidAccelEvents = null,
        public ?int $idleTimeMinutes = null,
        public ?CarbonImmutable $periodStart = null,
        public ?CarbonImmutable $periodEnd = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['periodStart', 'periodEnd', 'createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'          => 'companyId',
            'driver_id'           => 'driverId',
            'overall_score'       => 'overallScore',
            'safety_score'        => 'safetyScore',
            'efficiency_score'    => 'efficiencyScore',
            'compliance_score'    => 'complianceScore',
            'total_miles'         => 'totalMiles',
            'total_events'        => 'totalEvents',
            'hard_braking_events' => 'hardBrakingEvents',
            'speeding_events'     => 'speedingEvents',
            'rapid_accel_events'  => 'rapidAccelEvents',
            'idle_time_minutes'   => 'idleTimeMinutes',
            'period_start'        => 'periodStart',
            'period_end'          => 'periodEnd',
            'created_at'          => 'createdAt',
        ];
    }
}

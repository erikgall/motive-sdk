<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\TimecardStatus;

/**
 * Timecard data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Timecard extends DataTransferObject
{
    /**
     * @param  array<int, TimecardEntry>  $entries
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public int $driverId,
        public string $date,
        public TimecardStatus $status,
        public ?float $totalHours = null,
        public ?float $regularHours = null,
        public ?float $overtimeHours = null,
        public ?int $breakTime = null,
        public ?int $approvedById = null,
        public ?CarbonImmutable $approvedAt = null,
        public array $entries = [],
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['approvedAt', 'createdAt', 'updatedAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'status' => TimecardStatus::class,
        ];
    }

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [
            'entries' => TimecardEntry::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'     => 'companyId',
            'driver_id'      => 'driverId',
            'total_hours'    => 'totalHours',
            'regular_hours'  => 'regularHours',
            'overtime_hours' => 'overtimeHours',
            'break_time'     => 'breakTime',
            'approved_by_id' => 'approvedById',
            'approved_at'    => 'approvedAt',
            'created_at'     => 'createdAt',
            'updated_at'     => 'updatedAt',
        ];
    }
}

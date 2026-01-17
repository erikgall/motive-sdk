<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Timecard entry data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class TimecardEntry extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $timecardId,
        public string $entryType,
        public ?CarbonImmutable $startTime = null,
        public ?CarbonImmutable $endTime = null,
        public ?int $duration = null,
        public ?string $notes = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['startTime', 'endTime'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'timecard_id' => 'timecardId',
            'entry_type'  => 'entryType',
            'start_time'  => 'startTime',
            'end_time'    => 'endTime',
        ];
    }
}

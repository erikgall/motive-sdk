<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\DutyStatus;

/**
 * Hours of Service log entry data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosLog extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $driverId,
        public DutyStatus $dutyStatus,
        public CarbonImmutable $startTime,
        public ?int $vehicleId = null,
        public ?CarbonImmutable $endTime = null,
        public ?int $duration = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $location = null,
        public ?float $odometer = null,
        public ?string $annotation = null,
        public ?bool $certified = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}
}

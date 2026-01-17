<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Hours of Service availability data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosAvailability extends DataTransferObject
{
    public function __construct(
        public int $driverId,
        public ?int $driveTimeRemaining = null,
        public ?int $shiftTimeRemaining = null,
        public ?int $cycleTimeRemaining = null,
        public ?int $breakTimeRequired = null,
        public ?int $cycleTomorrow = null,
        public ?int $recap = null,
        public ?CarbonImmutable $lastCalculatedAt = null
    ) {}
}

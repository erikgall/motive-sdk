<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\VehicleStatus;

/**
 * Vehicle data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Vehicle extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public string $number,
        public ?string $make = null,
        public ?string $model = null,
        public ?int $year = null,
        public ?string $vin = null,
        public ?VehicleStatus $status = null,
        public ?string $licensePlateNumber = null,
        public ?string $licensePlateState = null,
        public ?int $currentDriverId = null,
        public ?string $externalId = null,
        public ?VehicleLocation $currentLocation = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}
}

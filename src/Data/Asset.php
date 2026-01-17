<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\AssetType;
use Motive\Enums\AssetStatus;

/**
 * Asset data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Asset extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $companyId = null,
        public ?AssetType $assetType = null,
        public ?AssetStatus $status = null,
        public ?string $serialNumber = null,
        public ?string $make = null,
        public ?string $model = null,
        public ?int $year = null,
        public ?string $licensePlateNumber = null,
        public ?string $licensePlateState = null,
        public ?string $vin = null,
        public ?int $vehicleId = null,
        public ?string $externalId = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}
}

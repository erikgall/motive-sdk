<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Driver data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Driver extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public ?int $userId = null,
        public ?int $companyId = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $licenseNumber = null,
        public ?string $licenseState = null,
        public ?CarbonImmutable $licenseExpiration = null,
        public ?string $carrierName = null,
        public ?string $carrierDotNumber = null,
        public ?string $eldMode = null,
        public ?bool $eldExempt = null,
        public ?string $externalId = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}
}

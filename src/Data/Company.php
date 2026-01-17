<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Company data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Company extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $dotNumber = null,
        public ?string $mcNumber = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zip = null,
        public ?string $country = null,
        public ?string $phone = null,
        public ?string $timezone = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}
}

<?php

namespace Motive\Data;

use Motive\Enums\UserRole;
use Carbon\CarbonImmutable;
use Motive\Enums\UserStatus;

/**
 * User data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class User extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $email,
        public ?int $companyId = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?UserRole $role = null,
        public ?UserStatus $status = null,
        public ?Driver $driver = null,
        public ?string $externalId = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}
}

<?php

namespace Motive\Data;

use Motive\Enums\UserRole;
use Carbon\CarbonImmutable;
use Motive\Enums\UserStatus;

/**
 * User data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $email
 * @property int|null $companyId
 * @property string|null $firstName
 * @property string|null $lastName
 * @property string|null $phone
 * @property UserRole|null $role
 * @property UserStatus|null $status
 * @property Driver|null $driver
 * @property string|null $externalId
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class User extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'companyId' => 'int',
        'role'      => UserRole::class,
        'status'    => UserStatus::class,
        'driver'    => Driver::class,
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];
}

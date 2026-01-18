<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Driver data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $firstName
 * @property string $lastName
 * @property int|null $userId
 * @property int|null $companyId
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $licenseNumber
 * @property string|null $licenseState
 * @property CarbonImmutable|null $licenseExpiration
 * @property string|null $carrierName
 * @property string|null $carrierDotNumber
 * @property string|null $eldMode
 * @property bool|null $eldExempt
 * @property string|null $externalId
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Driver extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'                => 'int',
        'userId'            => 'int',
        'companyId'         => 'int',
        'eldExempt'         => 'bool',
        'licenseExpiration' => CarbonImmutable::class,
        'createdAt'         => CarbonImmutable::class,
        'updatedAt'         => CarbonImmutable::class,
    ];
}

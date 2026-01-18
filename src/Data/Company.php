<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Company data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $name
 * @property string|null $dotNumber
 * @property string|null $mcNumber
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $timezone
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Company extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];
}

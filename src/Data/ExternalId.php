<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * External ID mapping data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $resourceType
 * @property int $resourceId
 * @property string $externalId
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class ExternalId extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'resourceId' => 'int',
        'createdAt'  => CarbonImmutable::class,
        'updatedAt'  => CarbonImmutable::class,
    ];
}

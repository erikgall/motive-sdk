<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Group member data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $groupId
 * @property int $memberId
 * @property string $memberType
 * @property CarbonImmutable|null $createdAt
 */
class GroupMember extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'groupId'   => 'int',
        'memberId'  => 'int',
        'createdAt' => CarbonImmutable::class,
    ];
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Group member data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class GroupMember extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $groupId,
        public int $memberId,
        public string $memberType,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'group_id'    => 'groupId',
            'member_id'   => 'memberId',
            'member_type' => 'memberType',
            'created_at'  => 'createdAt',
        ];
    }
}

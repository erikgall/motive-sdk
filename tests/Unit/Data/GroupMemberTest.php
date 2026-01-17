<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\GroupMember;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GroupMemberTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $member = GroupMember::from([
            'id'          => 123,
            'group_id'    => 456,
            'member_id'   => 789,
            'member_type' => 'driver',
        ]);

        $array = $member->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['group_id']);
        $this->assertSame(789, $array['member_id']);
        $this->assertSame('driver', $array['member_type']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $member = GroupMember::from([
            'id'          => 123,
            'group_id'    => 456,
            'member_id'   => 789,
            'member_type' => 'driver',
            'created_at'  => '2024-01-10T10:00:00Z',
        ]);

        $this->assertSame(123, $member->id);
        $this->assertSame(456, $member->groupId);
        $this->assertSame(789, $member->memberId);
        $this->assertSame('driver', $member->memberType);
        $this->assertInstanceOf(CarbonImmutable::class, $member->createdAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $member = GroupMember::from([
            'id'          => 123,
            'group_id'    => 456,
            'member_id'   => 789,
            'member_type' => 'vehicle',
        ]);

        $this->assertSame(123, $member->id);
        $this->assertNull($member->createdAt);
    }
}

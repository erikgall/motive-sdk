<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Group;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GroupTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $group = Group::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Group A',
        ]);

        $array = $group->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('Group A', $array['name']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $group = Group::from([
            'id'          => 123,
            'company_id'  => 456,
            'name'        => 'West Coast Drivers',
            'description' => 'Drivers in the west coast region',
            'external_id' => 'EXT-GRP-123',
            'created_at'  => '2024-01-10T10:00:00Z',
            'updated_at'  => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $group->id);
        $this->assertSame(456, $group->companyId);
        $this->assertSame('West Coast Drivers', $group->name);
        $this->assertSame('Drivers in the west coast region', $group->description);
        $this->assertSame('EXT-GRP-123', $group->externalId);
        $this->assertInstanceOf(CarbonImmutable::class, $group->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $group->updatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $group = Group::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Group A',
        ]);

        $this->assertSame(123, $group->id);
        $this->assertSame('Group A', $group->name);
        $this->assertNull($group->description);
        $this->assertNull($group->externalId);
        $this->assertNull($group->createdAt);
        $this->assertNull($group->updatedAt);
    }
}

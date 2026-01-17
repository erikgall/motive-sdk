<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\ExternalId;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ExternalIdTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $externalId = ExternalId::from([
            'id'            => 123,
            'resource_type' => 'vehicle',
            'resource_id'   => 789,
            'external_id'   => 'VEH-001',
        ]);

        $array = $externalId->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('vehicle', $array['resource_type']);
        $this->assertSame(789, $array['resource_id']);
        $this->assertSame('VEH-001', $array['external_id']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $externalId = ExternalId::from([
            'id'            => 123,
            'resource_type' => 'driver',
            'resource_id'   => 456,
            'external_id'   => 'EXT-001',
            'created_at'    => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $externalId->id);
        $this->assertSame('driver', $externalId->resourceType);
        $this->assertSame(456, $externalId->resourceId);
        $this->assertSame('EXT-001', $externalId->externalId);
        $this->assertInstanceOf(CarbonImmutable::class, $externalId->createdAt);
    }
}

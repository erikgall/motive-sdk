<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Location;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class LocationTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $location = Location::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Warehouse A',
            'latitude'   => 37.7749,
            'longitude'  => -122.4194,
        ]);

        $array = $location->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('Warehouse A', $array['name']);
        $this->assertSame(37.7749, $array['latitude']);
        $this->assertSame(-122.4194, $array['longitude']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $location = Location::from([
            'id'          => 123,
            'company_id'  => 456,
            'name'        => 'Warehouse A',
            'address'     => '123 Main St',
            'city'        => 'San Francisco',
            'state'       => 'CA',
            'postal_code' => '94105',
            'country'     => 'US',
            'latitude'    => 37.7749,
            'longitude'   => -122.4194,
            'external_id' => 'EXT-LOC-123',
            'created_at'  => '2024-01-10T10:00:00Z',
            'updated_at'  => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $location->id);
        $this->assertSame(456, $location->companyId);
        $this->assertSame('Warehouse A', $location->name);
        $this->assertSame('123 Main St', $location->address);
        $this->assertSame('San Francisco', $location->city);
        $this->assertSame('CA', $location->state);
        $this->assertSame('94105', $location->postalCode);
        $this->assertSame('US', $location->country);
        $this->assertSame(37.7749, $location->latitude);
        $this->assertSame(-122.4194, $location->longitude);
        $this->assertSame('EXT-LOC-123', $location->externalId);
        $this->assertInstanceOf(CarbonImmutable::class, $location->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $location->updatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $location = Location::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Location A',
        ]);

        $this->assertSame(123, $location->id);
        $this->assertSame('Location A', $location->name);
        $this->assertNull($location->address);
        $this->assertNull($location->city);
        $this->assertNull($location->state);
        $this->assertNull($location->postalCode);
        $this->assertNull($location->country);
        $this->assertNull($location->latitude);
        $this->assertNull($location->longitude);
        $this->assertNull($location->externalId);
    }
}

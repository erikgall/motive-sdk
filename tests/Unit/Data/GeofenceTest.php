<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Geofence;
use Carbon\CarbonImmutable;
use Motive\Enums\GeofenceType;
use PHPUnit\Framework\TestCase;
use Motive\Data\GeofenceCoordinate;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofenceTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $geofence = Geofence::from([
            'id'            => 123,
            'company_id'    => 456,
            'name'          => 'Zone A',
            'geofence_type' => 'circle',
            'radius'        => 100,
        ]);

        $array = $geofence->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('Zone A', $array['name']);
        $this->assertSame('circle', $array['geofence_type']);
        $this->assertSame(100, $array['radius']);
    }

    #[Test]
    public function it_creates_circle_geofence(): void
    {
        $geofence = Geofence::from([
            'id'            => 123,
            'company_id'    => 456,
            'name'          => 'Warehouse Zone',
            'geofence_type' => 'circle',
            'latitude'      => 37.7749,
            'longitude'     => -122.4194,
            'radius'        => 500,
            'external_id'   => 'EXT-GEO-123',
            'created_at'    => '2024-01-10T10:00:00Z',
            'updated_at'    => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $geofence->id);
        $this->assertSame(456, $geofence->companyId);
        $this->assertSame('Warehouse Zone', $geofence->name);
        $this->assertSame(GeofenceType::Circle, $geofence->geofenceType);
        $this->assertSame(37.7749, $geofence->latitude);
        $this->assertSame(-122.4194, $geofence->longitude);
        $this->assertSame(500, $geofence->radius);
        $this->assertSame('EXT-GEO-123', $geofence->externalId);
        $this->assertInstanceOf(CarbonImmutable::class, $geofence->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $geofence->updatedAt);
    }

    #[Test]
    public function it_creates_polygon_geofence_with_coordinates(): void
    {
        $geofence = Geofence::from([
            'id'            => 123,
            'company_id'    => 456,
            'name'          => 'Delivery Zone',
            'geofence_type' => 'polygon',
            'coordinates'   => [
                ['latitude' => 37.7749, 'longitude' => -122.4194, 'sequence' => 1],
                ['latitude' => 37.7849, 'longitude' => -122.4294, 'sequence' => 2],
                ['latitude' => 37.7649, 'longitude' => -122.4094, 'sequence' => 3],
            ],
        ]);

        $this->assertSame(GeofenceType::Polygon, $geofence->geofenceType);
        $this->assertCount(3, $geofence->coordinates);
        $this->assertInstanceOf(GeofenceCoordinate::class, $geofence->coordinates[0]);
        $this->assertSame(37.7749, $geofence->coordinates[0]->latitude);
        $this->assertSame(1, $geofence->coordinates[0]->sequence);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $geofence = Geofence::from([
            'id'            => 123,
            'company_id'    => 456,
            'name'          => 'Zone A',
            'geofence_type' => 'circle',
        ]);

        $this->assertSame(123, $geofence->id);
        $this->assertNull($geofence->latitude);
        $this->assertNull($geofence->longitude);
        $this->assertNull($geofence->radius);
        $this->assertNull($geofence->externalId);
        $this->assertEmpty($geofence->coordinates);
    }
}

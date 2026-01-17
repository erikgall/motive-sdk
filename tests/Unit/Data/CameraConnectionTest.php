<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\CameraType;
use PHPUnit\Framework\TestCase;
use Motive\Data\CameraConnection;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class CameraConnectionTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $connection = CameraConnection::from([
            'id'          => 123,
            'vehicle_id'  => 456,
            'camera_type' => 'dashcam',
            'connected'   => true,
        ]);

        $array = $connection->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['vehicle_id']);
        $this->assertSame('dashcam', $array['camera_type']);
        $this->assertTrue($array['connected']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $connection = CameraConnection::from([
            'id'            => 123,
            'vehicle_id'    => 456,
            'camera_type'   => 'dashcam',
            'serial_number' => 'CAM-001',
            'connected'     => true,
            'last_seen_at'  => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $connection->id);
        $this->assertSame(456, $connection->vehicleId);
        $this->assertSame(CameraType::Dashcam, $connection->cameraType);
        $this->assertSame('CAM-001', $connection->serialNumber);
        $this->assertTrue($connection->connected);
        $this->assertInstanceOf(CarbonImmutable::class, $connection->lastSeenAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $connection = CameraConnection::from([
            'id'          => 123,
            'vehicle_id'  => 456,
            'camera_type' => 'road_facing',
        ]);

        $this->assertNull($connection->serialNumber);
        $this->assertFalse($connection->connected);
        $this->assertNull($connection->lastSeenAt);
    }
}

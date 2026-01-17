<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\VehicleGateway;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleGatewayTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $gateway = VehicleGateway::from([
            'id'            => 123,
            'vehicle_id'    => 456,
            'serial_number' => 'GW-001',
            'connected'     => true,
        ]);

        $array = $gateway->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['vehicle_id']);
        $this->assertSame('GW-001', $array['serial_number']);
        $this->assertTrue($array['connected']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $gateway = VehicleGateway::from([
            'id'               => 123,
            'vehicle_id'       => 456,
            'serial_number'    => 'GW-001',
            'firmware_version' => '1.2.3',
            'connected'        => true,
            'last_seen_at'     => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $gateway->id);
        $this->assertSame(456, $gateway->vehicleId);
        $this->assertSame('GW-001', $gateway->serialNumber);
        $this->assertSame('1.2.3', $gateway->firmwareVersion);
        $this->assertTrue($gateway->connected);
        $this->assertInstanceOf(CarbonImmutable::class, $gateway->lastSeenAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $gateway = VehicleGateway::from([
            'id'            => 123,
            'vehicle_id'    => 456,
            'serial_number' => 'GW-001',
        ]);

        $this->assertNull($gateway->firmwareVersion);
        $this->assertFalse($gateway->connected);
        $this->assertNull($gateway->lastSeenAt);
    }
}

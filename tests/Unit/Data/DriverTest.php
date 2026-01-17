<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Driver;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DriverTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $driver = Driver::from([
            'id'         => 123,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
            'eld_exempt' => true,
        ]);

        $array = $driver->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('John', $array['first_name']);
        $this->assertSame('Doe', $array['last_name']);
        $this->assertSame('john@example.com', $array['email']);
        $this->assertTrue($array['eld_exempt']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $driver = Driver::from([
            'id'                 => 123,
            'user_id'            => 456,
            'company_id'         => 789,
            'first_name'         => 'John',
            'last_name'          => 'Doe',
            'email'              => 'john.doe@example.com',
            'phone'              => '+1-555-123-4567',
            'license_number'     => 'D1234567',
            'license_state'      => 'CA',
            'license_expiration' => '2025-12-31',
            'carrier_name'       => 'Acme Trucking',
            'carrier_dot_number' => '123456',
            'eld_mode'           => 'hos',
            'eld_exempt'         => false,
            'created_at'         => '2024-01-15T10:30:00Z',
            'updated_at'         => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $driver->id);
        $this->assertSame(456, $driver->userId);
        $this->assertSame(789, $driver->companyId);
        $this->assertSame('John', $driver->firstName);
        $this->assertSame('Doe', $driver->lastName);
        $this->assertSame('john.doe@example.com', $driver->email);
        $this->assertSame('+1-555-123-4567', $driver->phone);
        $this->assertSame('D1234567', $driver->licenseNumber);
        $this->assertSame('CA', $driver->licenseState);
        $this->assertInstanceOf(CarbonImmutable::class, $driver->licenseExpiration);
        $this->assertSame('Acme Trucking', $driver->carrierName);
        $this->assertSame('123456', $driver->carrierDotNumber);
        $this->assertSame('hos', $driver->eldMode);
        $this->assertFalse($driver->eldExempt);
        $this->assertInstanceOf(CarbonImmutable::class, $driver->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $driver->updatedAt);
    }

    #[Test]
    public function it_handles_external_id(): void
    {
        $driver = Driver::from([
            'id'          => 123,
            'first_name'  => 'John',
            'last_name'   => 'Doe',
            'external_id' => 'EXT-DRIVER-001',
        ]);

        $this->assertSame('EXT-DRIVER-001', $driver->externalId);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $driver = Driver::from([
            'id'         => 123,
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ]);

        $this->assertSame(123, $driver->id);
        $this->assertSame('John', $driver->firstName);
        $this->assertSame('Doe', $driver->lastName);
        $this->assertNull($driver->userId);
        $this->assertNull($driver->companyId);
        $this->assertNull($driver->email);
        $this->assertNull($driver->phone);
        $this->assertNull($driver->licenseNumber);
        $this->assertNull($driver->licenseState);
        $this->assertNull($driver->licenseExpiration);
        $this->assertNull($driver->carrierName);
        $this->assertNull($driver->carrierDotNumber);
        $this->assertNull($driver->eldMode);
        $this->assertNull($driver->eldExempt);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Vehicle;
use Carbon\CarbonImmutable;
use Motive\Enums\VehicleStatus;
use PHPUnit\Framework\TestCase;
use Motive\Data\VehicleLocation;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $vehicle = Vehicle::from([
            'id'         => 123,
            'company_id' => 456,
            'number'     => 'V-001',
            'make'       => 'Freightliner',
            'status'     => 'active',
        ]);

        $array = $vehicle->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('V-001', $array['number']);
        $this->assertSame('Freightliner', $array['make']);
        $this->assertSame('active', $array['status']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $vehicle = Vehicle::from([
            'id'                   => 123,
            'company_id'           => 456,
            'number'               => 'V-001',
            'make'                 => 'Freightliner',
            'model'                => 'Cascadia',
            'year'                 => 2022,
            'vin'                  => '1FUJGLDR5CLBK4321',
            'status'               => 'active',
            'license_plate_number' => 'ABC123',
            'license_plate_state'  => 'CA',
            'created_at'           => '2024-01-15T10:30:00Z',
            'updated_at'           => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $vehicle->id);
        $this->assertSame(456, $vehicle->companyId);
        $this->assertSame('V-001', $vehicle->number);
        $this->assertSame('Freightliner', $vehicle->make);
        $this->assertSame('Cascadia', $vehicle->model);
        $this->assertSame(2022, $vehicle->year);
        $this->assertSame('1FUJGLDR5CLBK4321', $vehicle->vin);
        $this->assertSame(VehicleStatus::Active, $vehicle->status);
        $this->assertSame('ABC123', $vehicle->licensePlateNumber);
        $this->assertSame('CA', $vehicle->licensePlateState);
        $this->assertInstanceOf(CarbonImmutable::class, $vehicle->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $vehicle->updatedAt);
    }

    #[Test]
    public function it_handles_current_driver_id(): void
    {
        $vehicle = Vehicle::from([
            'id'                => 123,
            'company_id'        => 456,
            'number'            => 'V-001',
            'current_driver_id' => 789,
        ]);

        $this->assertSame(789, $vehicle->currentDriverId);
    }

    #[Test]
    public function it_handles_external_ids(): void
    {
        $vehicle = Vehicle::from([
            'id'          => 123,
            'company_id'  => 456,
            'number'      => 'V-001',
            'external_id' => 'EXT-001',
        ]);

        $this->assertSame('EXT-001', $vehicle->externalId);
    }

    #[Test]
    public function it_handles_nested_current_location(): void
    {
        $vehicle = Vehicle::from([
            'id'               => 123,
            'company_id'       => 456,
            'number'           => 'V-001',
            'current_location' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
                'speed'     => 55.5,
            ],
        ]);

        $this->assertInstanceOf(VehicleLocation::class, $vehicle->currentLocation);
        $this->assertSame(37.7749, $vehicle->currentLocation->latitude);
        $this->assertSame(-122.4194, $vehicle->currentLocation->longitude);
        $this->assertSame(55.5, $vehicle->currentLocation->speed);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $vehicle = Vehicle::from([
            'id'         => 123,
            'company_id' => 456,
            'number'     => 'V-001',
        ]);

        $this->assertSame(123, $vehicle->id);
        $this->assertSame(456, $vehicle->companyId);
        $this->assertSame('V-001', $vehicle->number);
        $this->assertNull($vehicle->make);
        $this->assertNull($vehicle->model);
        $this->assertNull($vehicle->year);
        $this->assertNull($vehicle->vin);
        $this->assertNull($vehicle->status);
        $this->assertNull($vehicle->licensePlateNumber);
        $this->assertNull($vehicle->licensePlateState);
    }
}

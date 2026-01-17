<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Asset;
use Carbon\CarbonImmutable;
use Motive\Enums\AssetType;
use Motive\Enums\AssetStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AssetTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $asset = Asset::from([
            'id'         => 123,
            'name'       => 'Trailer 001',
            'asset_type' => 'trailer',
            'status'     => 'active',
        ]);

        $array = $asset->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('Trailer 001', $array['name']);
        $this->assertSame('trailer', $array['asset_type']);
        $this->assertSame('active', $array['status']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $asset = Asset::from([
            'id'                   => 123,
            'company_id'           => 456,
            'name'                 => 'Trailer 001',
            'asset_type'           => 'trailer',
            'status'               => 'active',
            'serial_number'        => 'TR-12345',
            'make'                 => 'Great Dane',
            'model'                => 'Everest',
            'year'                 => 2022,
            'license_plate_number' => 'TRL123',
            'license_plate_state'  => 'CA',
            'vin'                  => '1GRD8E5G2CG123456',
            'vehicle_id'           => 789,
            'created_at'           => '2024-01-15T10:30:00Z',
            'updated_at'           => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $asset->id);
        $this->assertSame(456, $asset->companyId);
        $this->assertSame('Trailer 001', $asset->name);
        $this->assertSame(AssetType::Trailer, $asset->assetType);
        $this->assertSame(AssetStatus::Active, $asset->status);
        $this->assertSame('TR-12345', $asset->serialNumber);
        $this->assertSame('Great Dane', $asset->make);
        $this->assertSame('Everest', $asset->model);
        $this->assertSame(2022, $asset->year);
        $this->assertSame('TRL123', $asset->licensePlateNumber);
        $this->assertSame('CA', $asset->licensePlateState);
        $this->assertSame('1GRD8E5G2CG123456', $asset->vin);
        $this->assertSame(789, $asset->vehicleId);
        $this->assertInstanceOf(CarbonImmutable::class, $asset->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $asset->updatedAt);
    }

    #[Test]
    public function it_handles_external_id(): void
    {
        $asset = Asset::from([
            'id'          => 123,
            'name'        => 'Trailer 001',
            'external_id' => 'EXT-ASSET-001',
        ]);

        $this->assertSame('EXT-ASSET-001', $asset->externalId);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $asset = Asset::from([
            'id'   => 123,
            'name' => 'Equipment 001',
        ]);

        $this->assertSame(123, $asset->id);
        $this->assertSame('Equipment 001', $asset->name);
        $this->assertNull($asset->companyId);
        $this->assertNull($asset->assetType);
        $this->assertNull($asset->status);
        $this->assertNull($asset->serialNumber);
        $this->assertNull($asset->make);
        $this->assertNull($asset->model);
        $this->assertNull($asset->year);
        $this->assertNull($asset->vehicleId);
    }
}

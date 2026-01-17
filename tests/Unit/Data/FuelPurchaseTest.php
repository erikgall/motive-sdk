<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\FuelPurchase;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FuelPurchaseTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $fuelPurchase = FuelPurchase::from([
            'id'         => 123,
            'company_id' => 456,
            'vehicle_id' => 789,
            'fuel_type'  => 'diesel',
            'quantity'   => 150.5,
            'total_cost' => 585.45,
        ]);

        $array = $fuelPurchase->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(789, $array['vehicle_id']);
        $this->assertSame('diesel', $array['fuel_type']);
        $this->assertSame(150.5, $array['quantity']);
        $this->assertSame(585.45, $array['total_cost']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $fuelPurchase = FuelPurchase::from([
            'id'             => 123,
            'company_id'     => 456,
            'vehicle_id'     => 789,
            'driver_id'      => 101,
            'fuel_type'      => 'diesel',
            'quantity'       => 150.5,
            'unit_price'     => 3.89,
            'total_cost'     => 585.45,
            'odometer'       => 125000,
            'vendor_name'    => 'Pilot Travel Center',
            'vendor_address' => '123 Highway Rd',
            'receipt_number' => 'REC-12345',
            'purchased_at'   => '2024-01-15T10:30:00Z',
            'created_at'     => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $fuelPurchase->id);
        $this->assertSame(456, $fuelPurchase->companyId);
        $this->assertSame(789, $fuelPurchase->vehicleId);
        $this->assertSame(101, $fuelPurchase->driverId);
        $this->assertSame('diesel', $fuelPurchase->fuelType);
        $this->assertSame(150.5, $fuelPurchase->quantity);
        $this->assertSame(3.89, $fuelPurchase->unitPrice);
        $this->assertSame(585.45, $fuelPurchase->totalCost);
        $this->assertSame(125000, $fuelPurchase->odometer);
        $this->assertSame('Pilot Travel Center', $fuelPurchase->vendorName);
        $this->assertSame('123 Highway Rd', $fuelPurchase->vendorAddress);
        $this->assertSame('REC-12345', $fuelPurchase->receiptNumber);
        $this->assertInstanceOf(CarbonImmutable::class, $fuelPurchase->purchasedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $fuelPurchase->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $fuelPurchase = FuelPurchase::from([
            'id'         => 123,
            'company_id' => 456,
            'fuel_type'  => 'gasoline',
            'quantity'   => 20.0,
            'total_cost' => 65.80,
        ]);

        $this->assertNull($fuelPurchase->vehicleId);
        $this->assertNull($fuelPurchase->driverId);
        $this->assertNull($fuelPurchase->unitPrice);
        $this->assertNull($fuelPurchase->odometer);
        $this->assertNull($fuelPurchase->vendorName);
        $this->assertNull($fuelPurchase->vendorAddress);
        $this->assertNull($fuelPurchase->receiptNumber);
        $this->assertNull($fuelPurchase->purchasedAt);
        $this->assertNull($fuelPurchase->createdAt);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use PHPUnit\Framework\TestCase;
use Motive\Data\IftaJurisdiction;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class IftaJurisdictionTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $jurisdiction = IftaJurisdiction::from([
            'jurisdiction_code' => 'CA',
            'jurisdiction_name' => 'California',
            'total_miles'       => 1500.5,
            'tax_due'           => 34.51,
        ]);

        $array = $jurisdiction->toArray();

        $this->assertSame('CA', $array['jurisdiction_code']);
        $this->assertSame('California', $array['jurisdiction_name']);
        $this->assertSame(1500.5, $array['total_miles']);
        $this->assertSame(34.51, $array['tax_due']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $jurisdiction = IftaJurisdiction::from([
            'jurisdiction_code' => 'CA',
            'jurisdiction_name' => 'California',
            'total_miles'       => 1500.5,
            'taxable_miles'     => 1450.0,
            'fuel_gallons'      => 250.75,
            'tax_paid_gallons'  => 200.0,
            'net_taxable'       => 50.75,
            'tax_rate'          => 0.68,
            'tax_due'           => 34.51,
        ]);

        $this->assertSame('CA', $jurisdiction->jurisdictionCode);
        $this->assertSame('California', $jurisdiction->jurisdictionName);
        $this->assertSame(1500.5, $jurisdiction->totalMiles);
        $this->assertSame(1450.0, $jurisdiction->taxableMiles);
        $this->assertSame(250.75, $jurisdiction->fuelGallons);
        $this->assertSame(200.0, $jurisdiction->taxPaidGallons);
        $this->assertSame(50.75, $jurisdiction->netTaxable);
        $this->assertSame(0.68, $jurisdiction->taxRate);
        $this->assertSame(34.51, $jurisdiction->taxDue);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $jurisdiction = IftaJurisdiction::from([
            'jurisdiction_code' => 'TX',
            'jurisdiction_name' => 'Texas',
            'total_miles'       => 500.0,
        ]);

        $this->assertNull($jurisdiction->taxableMiles);
        $this->assertNull($jurisdiction->fuelGallons);
        $this->assertNull($jurisdiction->taxPaidGallons);
        $this->assertNull($jurisdiction->netTaxable);
        $this->assertNull($jurisdiction->taxRate);
        $this->assertNull($jurisdiction->taxDue);
    }
}

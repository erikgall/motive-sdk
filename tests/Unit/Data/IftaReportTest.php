<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\IftaReport;
use PHPUnit\Framework\TestCase;
use Motive\Data\IftaJurisdiction;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class IftaReportTest extends TestCase
{
    #[Test]
    public function it_casts_jurisdictions_array(): void
    {
        $report = IftaReport::from([
            'id'            => 123,
            'company_id'    => 456,
            'quarter'       => 1,
            'year'          => 2024,
            'jurisdictions' => [
                [
                    'jurisdiction_code' => 'CA',
                    'jurisdiction_name' => 'California',
                    'total_miles'       => 1500.5,
                ],
                [
                    'jurisdiction_code' => 'TX',
                    'jurisdiction_name' => 'Texas',
                    'total_miles'       => 2500.0,
                ],
            ],
        ]);

        $this->assertCount(2, $report->jurisdictions);
        $this->assertInstanceOf(IftaJurisdiction::class, $report->jurisdictions[0]);
        $this->assertSame('CA', $report->jurisdictions[0]->jurisdictionCode);
        $this->assertSame('Texas', $report->jurisdictions[1]->jurisdictionName);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $report = IftaReport::from([
            'id'          => 123,
            'company_id'  => 456,
            'quarter'     => 1,
            'year'        => 2024,
            'total_miles' => 25000.5,
        ]);

        $array = $report->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(1, $array['quarter']);
        $this->assertSame(2024, $array['year']);
        $this->assertSame(25000.5, $array['total_miles']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $report = IftaReport::from([
            'id'            => 123,
            'company_id'    => 456,
            'quarter'       => 1,
            'year'          => 2024,
            'total_miles'   => 25000.5,
            'total_gallons' => 4500.25,
            'mpg'           => 5.56,
            'total_tax_due' => 1250.75,
            'status'        => 'completed',
            'generated_at'  => '2024-04-15T10:30:00Z',
            'created_at'    => '2024-04-15T10:00:00Z',
        ]);

        $this->assertSame(123, $report->id);
        $this->assertSame(456, $report->companyId);
        $this->assertSame(1, $report->quarter);
        $this->assertSame(2024, $report->year);
        $this->assertSame(25000.5, $report->totalMiles);
        $this->assertSame(4500.25, $report->totalGallons);
        $this->assertSame(5.56, $report->mpg);
        $this->assertSame(1250.75, $report->totalTaxDue);
        $this->assertSame('completed', $report->status);
        $this->assertInstanceOf(CarbonImmutable::class, $report->generatedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $report->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $report = IftaReport::from([
            'id'         => 123,
            'company_id' => 456,
            'quarter'    => 1,
            'year'       => 2024,
        ]);

        $this->assertNull($report->totalMiles);
        $this->assertNull($report->totalGallons);
        $this->assertNull($report->mpg);
        $this->assertNull($report->totalTaxDue);
        $this->assertNull($report->status);
        $this->assertNull($report->generatedAt);
        $this->assertNull($report->createdAt);
        $this->assertEmpty($report->jurisdictions);
    }
}

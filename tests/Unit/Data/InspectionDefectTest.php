<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Data\InspectionDefect;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionDefectTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $defect = InspectionDefect::from([
            'id'            => 123,
            'inspection_id' => 456,
            'category'      => 'brakes',
            'description'   => 'Worn brake pads',
        ]);

        $array = $defect->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['inspection_id']);
        $this->assertSame('brakes', $array['category']);
        $this->assertSame('Worn brake pads', $array['description']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $defect = InspectionDefect::from([
            'id'              => 123,
            'inspection_id'   => 456,
            'category'        => 'brakes',
            'description'     => 'Worn brake pads',
            'severity'        => 'major',
            'corrected'       => false,
            'corrected_at'    => null,
            'corrected_by_id' => null,
            'notes'           => 'Needs immediate attention',
            'created_at'      => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $defect->id);
        $this->assertSame(456, $defect->inspectionId);
        $this->assertSame('brakes', $defect->category);
        $this->assertSame('Worn brake pads', $defect->description);
        $this->assertSame('major', $defect->severity);
        $this->assertFalse($defect->corrected);
        $this->assertNull($defect->correctedAt);
        $this->assertNull($defect->correctedById);
        $this->assertSame('Needs immediate attention', $defect->notes);
        $this->assertInstanceOf(CarbonImmutable::class, $defect->createdAt);
    }

    #[Test]
    public function it_handles_corrected_defect(): void
    {
        $defect = InspectionDefect::from([
            'id'              => 123,
            'inspection_id'   => 456,
            'category'        => 'lights',
            'description'     => 'Broken tail light',
            'corrected'       => true,
            'corrected_at'    => '2024-01-15T10:00:00Z',
            'corrected_by_id' => 789,
        ]);

        $this->assertTrue($defect->corrected);
        $this->assertInstanceOf(CarbonImmutable::class, $defect->correctedAt);
        $this->assertSame(789, $defect->correctedById);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $defect = InspectionDefect::from([
            'id'            => 123,
            'inspection_id' => 456,
            'category'      => 'tires',
            'description'   => 'Low pressure',
        ]);

        $this->assertSame(123, $defect->id);
        $this->assertNull($defect->severity);
        $this->assertNull($defect->corrected);
        $this->assertNull($defect->notes);
        $this->assertNull($defect->createdAt);
    }
}

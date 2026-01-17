<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\FaultCode;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FaultCodeTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $faultCode = FaultCode::from([
            'id'         => 123,
            'vehicle_id' => 456,
            'code'       => 'P0300',
            'source'     => 'engine',
        ]);

        $array = $faultCode->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['vehicle_id']);
        $this->assertSame('P0300', $array['code']);
        $this->assertSame('engine', $array['source']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $faultCode = FaultCode::from([
            'id'                  => 123,
            'vehicle_id'          => 456,
            'code'                => 'P0300',
            'description'         => 'Random/Multiple Cylinder Misfire Detected',
            'source'              => 'engine',
            'spn'                 => 91,
            'fmi'                 => 3,
            'occurrence_count'    => 5,
            'first_occurrence_at' => '2024-01-10T08:00:00Z',
            'last_occurrence_at'  => '2024-01-15T16:00:00Z',
            'resolved'            => false,
            'resolved_at'         => null,
            'created_at'          => '2024-01-10T08:00:00Z',
        ]);

        $this->assertSame(123, $faultCode->id);
        $this->assertSame(456, $faultCode->vehicleId);
        $this->assertSame('P0300', $faultCode->code);
        $this->assertSame('Random/Multiple Cylinder Misfire Detected', $faultCode->description);
        $this->assertSame('engine', $faultCode->source);
        $this->assertSame(91, $faultCode->spn);
        $this->assertSame(3, $faultCode->fmi);
        $this->assertSame(5, $faultCode->occurrenceCount);
        $this->assertInstanceOf(CarbonImmutable::class, $faultCode->firstOccurrenceAt);
        $this->assertInstanceOf(CarbonImmutable::class, $faultCode->lastOccurrenceAt);
        $this->assertFalse($faultCode->resolved);
        $this->assertNull($faultCode->resolvedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $faultCode->createdAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $faultCode = FaultCode::from([
            'id'         => 123,
            'vehicle_id' => 456,
            'code'       => 'P0100',
        ]);

        $this->assertSame(123, $faultCode->id);
        $this->assertSame(456, $faultCode->vehicleId);
        $this->assertNull($faultCode->description);
        $this->assertNull($faultCode->source);
        $this->assertNull($faultCode->spn);
        $this->assertNull($faultCode->fmi);
        $this->assertNull($faultCode->occurrenceCount);
        $this->assertNull($faultCode->firstOccurrenceAt);
        $this->assertNull($faultCode->lastOccurrenceAt);
        $this->assertNull($faultCode->resolved);
        $this->assertNull($faultCode->resolvedAt);
    }

    #[Test]
    public function it_handles_resolved_fault(): void
    {
        $faultCode = FaultCode::from([
            'id'          => 123,
            'vehicle_id'  => 456,
            'code'        => 'P0420',
            'resolved'    => true,
            'resolved_at' => '2024-01-16T10:00:00Z',
        ]);

        $this->assertTrue($faultCode->resolved);
        $this->assertInstanceOf(CarbonImmutable::class, $faultCode->resolvedAt);
    }
}

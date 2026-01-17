<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Dispatch;
use Carbon\CarbonImmutable;
use Motive\Data\DispatchStop;
use PHPUnit\Framework\TestCase;
use Motive\Enums\DispatchStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $dispatch = Dispatch::from([
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'status'     => 'completed',
        ]);

        $array = $dispatch->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(789, $array['driver_id']);
        $this->assertSame('completed', $array['status']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $dispatch = Dispatch::from([
            'id'           => 123,
            'company_id'   => 456,
            'driver_id'    => 789,
            'vehicle_id'   => 101,
            'external_id'  => 'EXT-123',
            'status'       => 'in_progress',
            'reference'    => 'REF-ABC',
            'notes'        => 'Handle with care',
            'started_at'   => '2024-01-15T08:00:00Z',
            'completed_at' => null,
            'created_at'   => '2024-01-10T10:00:00Z',
            'updated_at'   => '2024-01-15T08:00:00Z',
        ]);

        $this->assertSame(123, $dispatch->id);
        $this->assertSame(456, $dispatch->companyId);
        $this->assertSame(789, $dispatch->driverId);
        $this->assertSame(101, $dispatch->vehicleId);
        $this->assertSame('EXT-123', $dispatch->externalId);
        $this->assertSame(DispatchStatus::InProgress, $dispatch->status);
        $this->assertSame('REF-ABC', $dispatch->reference);
        $this->assertSame('Handle with care', $dispatch->notes);
        $this->assertInstanceOf(CarbonImmutable::class, $dispatch->startedAt);
        $this->assertNull($dispatch->completedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $dispatch->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $dispatch->updatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $dispatch = Dispatch::from([
            'id'         => 123,
            'company_id' => 456,
            'status'     => 'pending',
        ]);

        $this->assertSame(123, $dispatch->id);
        $this->assertNull($dispatch->driverId);
        $this->assertNull($dispatch->vehicleId);
        $this->assertNull($dispatch->externalId);
        $this->assertNull($dispatch->reference);
        $this->assertNull($dispatch->notes);
        $this->assertNull($dispatch->startedAt);
        $this->assertNull($dispatch->completedAt);
        $this->assertEmpty($dispatch->stops);
    }

    #[Test]
    public function it_handles_stops_array(): void
    {
        $dispatch = Dispatch::from([
            'id'         => 123,
            'company_id' => 456,
            'status'     => 'pending',
            'stops'      => [
                [
                    'id'          => 1,
                    'dispatch_id' => 123,
                    'stop_type'   => 'pickup',
                    'name'        => 'Warehouse A',
                ],
                [
                    'id'          => 2,
                    'dispatch_id' => 123,
                    'stop_type'   => 'delivery',
                    'name'        => 'Customer B',
                ],
            ],
        ]);

        $this->assertCount(2, $dispatch->stops);
        $this->assertInstanceOf(DispatchStop::class, $dispatch->stops[0]);
        $this->assertSame('Warehouse A', $dispatch->stops[0]->name);
        $this->assertInstanceOf(DispatchStop::class, $dispatch->stops[1]);
        $this->assertSame('Customer B', $dispatch->stops[1]->name);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Timecard;
use Carbon\CarbonImmutable;
use Motive\Data\TimecardEntry;
use PHPUnit\Framework\TestCase;
use Motive\Enums\TimecardStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class TimecardTest extends TestCase
{
    #[Test]
    public function it_casts_entries_array(): void
    {
        $timecard = Timecard::from([
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'date'       => '2024-01-15',
            'status'     => 'approved',
            'entries'    => [
                ['id' => 1, 'timecard_id' => 123, 'entry_type' => 'work', 'duration' => 14400],
                ['id' => 2, 'timecard_id' => 123, 'entry_type' => 'break', 'duration' => 1800],
            ],
        ]);

        $this->assertCount(2, $timecard->entries);
        $this->assertInstanceOf(TimecardEntry::class, $timecard->entries[0]);
        $this->assertSame('work', $timecard->entries[0]->entryType);
        $this->assertSame('break', $timecard->entries[1]->entryType);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $timecard = Timecard::from([
            'id'          => 123,
            'company_id'  => 456,
            'driver_id'   => 789,
            'date'        => '2024-01-15',
            'status'      => 'approved',
            'total_hours' => 8.5,
        ]);

        $array = $timecard->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame(789, $array['driver_id']);
        $this->assertSame('2024-01-15', $array['date']);
        $this->assertSame('approved', $array['status']);
        $this->assertSame(8.5, $array['total_hours']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $timecard = Timecard::from([
            'id'             => 123,
            'company_id'     => 456,
            'driver_id'      => 789,
            'date'           => '2024-01-15',
            'status'         => 'approved',
            'total_hours'    => 8.5,
            'regular_hours'  => 8.0,
            'overtime_hours' => 0.5,
            'break_time'     => 30,
            'approved_by_id' => 101,
            'approved_at'    => '2024-01-16T10:00:00Z',
            'created_at'     => '2024-01-15T08:00:00Z',
            'updated_at'     => '2024-01-16T10:00:00Z',
        ]);

        $this->assertSame(123, $timecard->id);
        $this->assertSame(456, $timecard->companyId);
        $this->assertSame(789, $timecard->driverId);
        $this->assertSame('2024-01-15', $timecard->date);
        $this->assertSame(TimecardStatus::Approved, $timecard->status);
        $this->assertSame(8.5, $timecard->totalHours);
        $this->assertSame(8.0, $timecard->regularHours);
        $this->assertSame(0.5, $timecard->overtimeHours);
        $this->assertSame(30, $timecard->breakTime);
        $this->assertSame(101, $timecard->approvedById);
        $this->assertInstanceOf(CarbonImmutable::class, $timecard->approvedAt);
        $this->assertInstanceOf(CarbonImmutable::class, $timecard->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $timecard->updatedAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $timecard = Timecard::from([
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'date'       => '2024-01-15',
            'status'     => 'pending',
        ]);

        $this->assertNull($timecard->totalHours);
        $this->assertNull($timecard->regularHours);
        $this->assertNull($timecard->overtimeHours);
        $this->assertNull($timecard->breakTime);
        $this->assertNull($timecard->approvedById);
        $this->assertNull($timecard->approvedAt);
        $this->assertEmpty($timecard->entries);
    }
}

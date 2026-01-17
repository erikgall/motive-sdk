<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\TimecardEntry;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class TimecardEntryTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $entry = TimecardEntry::from([
            'id'          => 123,
            'timecard_id' => 456,
            'entry_type'  => 'work',
            'duration'    => 32400,
        ]);

        $array = $entry->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['timecard_id']);
        $this->assertSame('work', $array['entry_type']);
        $this->assertSame(32400, $array['duration']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $entry = TimecardEntry::from([
            'id'          => 123,
            'timecard_id' => 456,
            'entry_type'  => 'work',
            'start_time'  => '2024-01-15T08:00:00Z',
            'end_time'    => '2024-01-15T17:00:00Z',
            'duration'    => 32400,
            'notes'       => 'Regular work day',
        ]);

        $this->assertSame(123, $entry->id);
        $this->assertSame(456, $entry->timecardId);
        $this->assertSame('work', $entry->entryType);
        $this->assertInstanceOf(CarbonImmutable::class, $entry->startTime);
        $this->assertInstanceOf(CarbonImmutable::class, $entry->endTime);
        $this->assertSame(32400, $entry->duration);
        $this->assertSame('Regular work day', $entry->notes);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $entry = TimecardEntry::from([
            'id'          => 123,
            'timecard_id' => 456,
            'entry_type'  => 'break',
            'start_time'  => '2024-01-15T12:00:00Z',
        ]);

        $this->assertNull($entry->endTime);
        $this->assertNull($entry->duration);
        $this->assertNull($entry->notes);
    }
}

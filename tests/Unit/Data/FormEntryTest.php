<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\FormEntry;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormEntryTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $entry = FormEntry::from([
            'id'         => 123,
            'form_id'    => 456,
            'driver_id'  => 789,
            'vehicle_id' => 101,
        ]);

        $array = $entry->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['form_id']);
        $this->assertSame(789, $array['driver_id']);
        $this->assertSame(101, $array['vehicle_id']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $entry = FormEntry::from([
            'id'         => 123,
            'form_id'    => 456,
            'driver_id'  => 789,
            'created_at' => '2024-01-15T10:00:00Z',
            'updated_at' => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $entry->id);
        $this->assertSame(456, $entry->formId);
        $this->assertSame(789, $entry->driverId);
        $this->assertInstanceOf(CarbonImmutable::class, $entry->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $entry->updatedAt);
    }

    #[Test]
    public function it_handles_field_values_array(): void
    {
        $entry = FormEntry::from([
            'id'           => 123,
            'form_id'      => 456,
            'driver_id'    => 789,
            'field_values' => [
                ['field_id' => 1, 'value' => 'John Doe'],
                ['field_id' => 2, 'value' => 'Delivered successfully'],
            ],
        ]);

        $this->assertCount(2, $entry->fieldValues);
        $this->assertSame(1, $entry->fieldValues[0]['field_id']);
        $this->assertSame('John Doe', $entry->fieldValues[0]['value']);
    }

    #[Test]
    public function it_handles_location_data(): void
    {
        $entry = FormEntry::from([
            'id'        => 123,
            'form_id'   => 456,
            'driver_id' => 789,
            'location'  => [
                'lat' => 37.7749,
                'lng' => -122.4194,
            ],
        ]);

        $this->assertIsArray($entry->location);
        $this->assertSame(37.7749, $entry->location['lat']);
        $this->assertSame(-122.4194, $entry->location['lng']);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $entry = FormEntry::from([
            'id'        => 123,
            'form_id'   => 456,
            'driver_id' => 789,
        ]);

        $this->assertNull($entry->vehicleId);
        $this->assertNull($entry->submittedAt);
        $this->assertEmpty($entry->fieldValues);
        $this->assertNull($entry->location);
    }
}

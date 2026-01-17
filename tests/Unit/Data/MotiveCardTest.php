<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\MotiveCard;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveCardTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $card = MotiveCard::from([
            'id'          => 123,
            'card_number' => '****1234',
            'driver_id'   => 456,
            'active'      => true,
        ]);

        $array = $card->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('****1234', $array['card_number']);
        $this->assertSame(456, $array['driver_id']);
        $this->assertTrue($array['active']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $card = MotiveCard::from([
            'id'          => 123,
            'card_number' => '****1234',
            'driver_id'   => 456,
            'vehicle_id'  => 789,
            'active'      => true,
            'created_at'  => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $card->id);
        $this->assertSame('****1234', $card->cardNumber);
        $this->assertSame(456, $card->driverId);
        $this->assertSame(789, $card->vehicleId);
        $this->assertTrue($card->active);
        $this->assertInstanceOf(CarbonImmutable::class, $card->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $card = MotiveCard::from([
            'id'          => 123,
            'card_number' => '****1234',
        ]);

        $this->assertNull($card->driverId);
        $this->assertNull($card->vehicleId);
        $this->assertTrue($card->active);
        $this->assertNull($card->expiresAt);
    }
}

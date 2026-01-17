<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\CardLimit;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class CardLimitTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $limit = CardLimit::from([
            'id'          => 123,
            'card_id'     => 456,
            'daily_limit' => 500.00,
        ]);

        $array = $limit->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['card_id']);
        $this->assertSame(500.00, $array['daily_limit']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $limit = CardLimit::from([
            'id'              => 123,
            'card_id'         => 456,
            'daily_limit'     => 500.00,
            'weekly_limit'    => 2000.00,
            'monthly_limit'   => 8000.00,
            'per_transaction' => 200.00,
            'fuel_only'       => true,
        ]);

        $this->assertSame(123, $limit->id);
        $this->assertSame(456, $limit->cardId);
        $this->assertSame(500.00, $limit->dailyLimit);
        $this->assertSame(2000.00, $limit->weeklyLimit);
        $this->assertSame(8000.00, $limit->monthlyLimit);
        $this->assertSame(200.00, $limit->perTransaction);
        $this->assertTrue($limit->fuelOnly);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $limit = CardLimit::from([
            'id'      => 123,
            'card_id' => 456,
        ]);

        $this->assertNull($limit->dailyLimit);
        $this->assertNull($limit->weeklyLimit);
        $this->assertNull($limit->monthlyLimit);
        $this->assertNull($limit->perTransaction);
        $this->assertFalse($limit->fuelOnly);
    }
}

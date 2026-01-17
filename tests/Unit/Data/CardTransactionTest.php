<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Data\CardTransaction;
use Motive\Enums\CardTransactionType;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class CardTransactionTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $transaction = CardTransaction::from([
            'id'               => 123,
            'card_id'          => 456,
            'amount'           => 75.00,
            'transaction_type' => 'fuel',
            'driver_id'        => 789,
        ]);

        $array = $transaction->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['card_id']);
        $this->assertSame(75.00, $array['amount']);
        $this->assertSame('fuel', $array['transaction_type']);
        $this->assertSame(789, $array['driver_id']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $transaction = CardTransaction::from([
            'id'               => 123,
            'card_id'          => 456,
            'amount'           => 75.50,
            'transaction_type' => 'fuel',
            'merchant_name'    => 'Shell Gas Station',
            'transaction_date' => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $transaction->id);
        $this->assertSame(456, $transaction->cardId);
        $this->assertSame(75.50, $transaction->amount);
        $this->assertSame(CardTransactionType::Fuel, $transaction->transactionType);
        $this->assertSame('Shell Gas Station', $transaction->merchantName);
        $this->assertInstanceOf(CarbonImmutable::class, $transaction->transactionDate);
    }

    #[Test]
    public function it_handles_location_data(): void
    {
        $transaction = CardTransaction::from([
            'id'               => 123,
            'card_id'          => 456,
            'amount'           => 75.00,
            'transaction_type' => 'fuel',
            'location'         => [
                'lat' => 37.7749,
                'lng' => -122.4194,
            ],
        ]);

        $this->assertIsArray($transaction->location);
        $this->assertSame(37.7749, $transaction->location['lat']);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $transaction = CardTransaction::from([
            'id'               => 123,
            'card_id'          => 456,
            'amount'           => 50.00,
            'transaction_type' => 'other',
        ]);

        $this->assertNull($transaction->merchantName);
        $this->assertNull($transaction->transactionDate);
        $this->assertNull($transaction->driverId);
        $this->assertNull($transaction->vehicleId);
        $this->assertNull($transaction->location);
    }
}

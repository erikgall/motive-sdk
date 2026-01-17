<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\CardTransactionType;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class CardTransactionTypeTest extends TestCase
{
    #[Test]
    public function it_creates_from_value(): void
    {
        $this->assertSame(CardTransactionType::Fuel, CardTransactionType::from('fuel'));
        $this->assertSame(CardTransactionType::Maintenance, CardTransactionType::from('maintenance'));
        $this->assertSame(CardTransactionType::Toll, CardTransactionType::from('toll'));
    }

    #[Test]
    public function it_has_expected_cases(): void
    {
        $cases = CardTransactionType::cases();

        $this->assertContains(CardTransactionType::Fuel, $cases);
        $this->assertContains(CardTransactionType::Maintenance, $cases);
        $this->assertContains(CardTransactionType::Toll, $cases);
        $this->assertContains(CardTransactionType::Parking, $cases);
        $this->assertContains(CardTransactionType::Other, $cases);
    }
}

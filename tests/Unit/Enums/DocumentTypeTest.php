<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\DocumentType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $type = DocumentType::from('bill_of_lading');

        $this->assertSame(DocumentType::BillOfLading, $type);
    }

    #[Test]
    public function it_has_bill_of_lading_type(): void
    {
        $this->assertSame('bill_of_lading', DocumentType::BillOfLading->value);
    }

    #[Test]
    public function it_has_delivery_receipt_type(): void
    {
        $this->assertSame('delivery_receipt', DocumentType::DeliveryReceipt->value);
    }

    #[Test]
    public function it_has_fuel_receipt_type(): void
    {
        $this->assertSame('fuel_receipt', DocumentType::FuelReceipt->value);
    }

    #[Test]
    public function it_has_other_type(): void
    {
        $this->assertSame('other', DocumentType::Other->value);
    }

    #[Test]
    public function it_has_scale_ticket_type(): void
    {
        $this->assertSame('scale_ticket', DocumentType::ScaleTicket->value);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Document;
use Carbon\CarbonImmutable;
use Motive\Data\DocumentImage;
use Motive\Enums\DocumentType;
use PHPUnit\Framework\TestCase;
use Motive\Enums\DocumentStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentTest extends TestCase
{
    #[Test]
    public function it_casts_images_array(): void
    {
        $document = Document::from([
            'id'            => 123,
            'company_id'    => 456,
            'document_type' => 'fuel_receipt',
            'status'        => 'pending',
            'images'        => [
                ['id' => 1, 'url' => 'https://example.com/image1.jpg'],
                ['id' => 2, 'url' => 'https://example.com/image2.jpg'],
            ],
        ]);

        $this->assertCount(2, $document->images);
        $this->assertInstanceOf(DocumentImage::class, $document->images[0]);
        $this->assertSame('https://example.com/image1.jpg', $document->images[0]->url);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $document = Document::from([
            'id'            => 123,
            'company_id'    => 456,
            'document_type' => 'delivery_receipt',
            'status'        => 'approved',
        ]);

        $array = $document->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('delivery_receipt', $array['document_type']);
        $this->assertSame('approved', $array['status']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $document = Document::from([
            'id'            => 123,
            'company_id'    => 456,
            'driver_id'     => 789,
            'document_type' => 'bill_of_lading',
            'status'        => 'pending',
            'description'   => 'Delivery document',
            'created_at'    => '2024-01-15T10:30:00Z',
        ]);

        $this->assertSame(123, $document->id);
        $this->assertSame(456, $document->companyId);
        $this->assertSame(789, $document->driverId);
        $this->assertSame(DocumentType::BillOfLading, $document->documentType);
        $this->assertSame(DocumentStatus::Pending, $document->status);
        $this->assertSame('Delivery document', $document->description);
        $this->assertInstanceOf(CarbonImmutable::class, $document->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $document = Document::from([
            'id'            => 123,
            'company_id'    => 456,
            'document_type' => 'other',
            'status'        => 'approved',
        ]);

        $this->assertNull($document->driverId);
        $this->assertNull($document->description);
        $this->assertNull($document->externalId);
        $this->assertNull($document->createdAt);
        $this->assertEmpty($document->images);
    }
}

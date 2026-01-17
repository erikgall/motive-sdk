<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\DocumentImage;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentImageTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $image = DocumentImage::from([
            'id'       => 123,
            'url'      => 'https://example.com/image.jpg',
            'filename' => 'receipt.jpg',
        ]);

        $array = $image->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('https://example.com/image.jpg', $array['url']);
        $this->assertSame('receipt.jpg', $array['filename']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $image = DocumentImage::from([
            'id'       => 123,
            'url'      => 'https://example.com/image.jpg',
            'filename' => 'receipt.jpg',
            'size'     => 102400,
            'sequence' => 1,
        ]);

        $this->assertSame(123, $image->id);
        $this->assertSame('https://example.com/image.jpg', $image->url);
        $this->assertSame('receipt.jpg', $image->filename);
        $this->assertSame(102400, $image->size);
        $this->assertSame(1, $image->sequence);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $image = DocumentImage::from([
            'id'  => 123,
            'url' => 'https://example.com/image.jpg',
        ]);

        $this->assertNull($image->filename);
        $this->assertNull($image->size);
        $this->assertNull($image->sequence);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Video;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VideoTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $video = Video::from([
            'id'         => 123,
            'request_id' => 456,
            'url'        => 'https://example.com/video.mp4',
            'duration'   => 1800,
        ]);

        $array = $video->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['request_id']);
        $this->assertSame('https://example.com/video.mp4', $array['url']);
        $this->assertSame(1800, $array['duration']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $video = Video::from([
            'id'         => 123,
            'request_id' => 456,
            'url'        => 'https://example.com/video.mp4',
            'duration'   => 1800,
            'file_size'  => 52428800,
            'created_at' => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $video->id);
        $this->assertSame(456, $video->requestId);
        $this->assertSame('https://example.com/video.mp4', $video->url);
        $this->assertSame(1800, $video->duration);
        $this->assertSame(52428800, $video->fileSize);
        $this->assertInstanceOf(CarbonImmutable::class, $video->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $video = Video::from([
            'id'         => 123,
            'request_id' => 456,
            'url'        => 'https://example.com/video.mp4',
        ]);

        $this->assertNull($video->duration);
        $this->assertNull($video->fileSize);
        $this->assertNull($video->thumbnailUrl);
        $this->assertNull($video->expiresAt);
    }
}

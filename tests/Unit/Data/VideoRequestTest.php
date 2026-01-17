<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\VideoRequest;
use Motive\Enums\VideoStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VideoRequestTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $request = VideoRequest::from([
            'id'         => 123,
            'vehicle_id' => 456,
            'status'     => 'completed',
            'video_url'  => 'https://example.com/video.mp4',
        ]);

        $array = $request->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['vehicle_id']);
        $this->assertSame('completed', $array['status']);
        $this->assertSame('https://example.com/video.mp4', $array['video_url']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $request = VideoRequest::from([
            'id'         => 123,
            'vehicle_id' => 456,
            'status'     => 'processing',
            'start_time' => '2024-01-15T08:00:00Z',
            'end_time'   => '2024-01-15T08:30:00Z',
            'created_at' => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $request->id);
        $this->assertSame(456, $request->vehicleId);
        $this->assertSame(VideoStatus::Processing, $request->status);
        $this->assertInstanceOf(CarbonImmutable::class, $request->startTime);
        $this->assertInstanceOf(CarbonImmutable::class, $request->endTime);
        $this->assertInstanceOf(CarbonImmutable::class, $request->createdAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $request = VideoRequest::from([
            'id'         => 123,
            'vehicle_id' => 456,
            'status'     => 'pending',
        ]);

        $this->assertNull($request->startTime);
        $this->assertNull($request->endTime);
        $this->assertNull($request->driverId);
        $this->assertNull($request->videoUrl);
    }
}

<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Message;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Enums\MessageDirection;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MessageTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $message = Message::from([
            'id'         => 123,
            'company_id' => 456,
            'body'       => 'Test message',
            'direction'  => 'outbound',
        ]);

        $array = $message->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('Test message', $array['body']);
        $this->assertSame('outbound', $array['direction']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $message = Message::from([
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'body'       => 'Please check your route.',
            'direction'  => 'outbound',
            'read'       => false,
            'sent_at'    => '2024-01-15T10:30:00Z',
            'created_at' => '2024-01-15T10:30:00Z',
        ]);

        $this->assertSame(123, $message->id);
        $this->assertSame(456, $message->companyId);
        $this->assertSame(789, $message->driverId);
        $this->assertSame('Please check your route.', $message->body);
        $this->assertSame(MessageDirection::Outbound, $message->direction);
        $this->assertFalse($message->read);
        $this->assertInstanceOf(CarbonImmutable::class, $message->sentAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $message = Message::from([
            'id'         => 123,
            'company_id' => 456,
            'body'       => 'Test message',
            'direction'  => 'inbound',
        ]);

        $this->assertNull($message->driverId);
        $this->assertNull($message->sentAt);
        $this->assertNull($message->createdAt);
        $this->assertNull($message->read);
    }
}

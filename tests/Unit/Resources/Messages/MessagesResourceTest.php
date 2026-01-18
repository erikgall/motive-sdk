<?php

namespace Motive\Tests\Unit\Resources\Messages;

use Motive\Data\Message;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Enums\MessageDirection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Messages\MessagesResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MessagesResourceTest extends TestCase
{
    #[Test]
    public function it_broadcasts_message(): void
    {
        $messagesData = [
            [
                'id'         => 1,
                'company_id' => 456,
                'driver_id'  => 101,
                'body'       => 'Broadcast message',
                'direction'  => 'outbound',
            ],
            [
                'id'         => 2,
                'company_id' => 456,
                'driver_id'  => 102,
                'body'       => 'Broadcast message',
                'direction'  => 'outbound',
            ],
        ];

        $response = $this->createMockResponse(['messages' => $messagesData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/messages/broadcast', ['message' => ['driver_ids' => [101, 102], 'body' => 'Broadcast message']])
            ->willReturn($response);

        $resource = new MessagesResource($client);
        $messages = $resource->broadcast([
            'driver_ids' => [101, 102],
            'body'       => 'Broadcast message',
        ]);

        $this->assertCount(2, $messages);
        $this->assertInstanceOf(Message::class, $messages->first());
    }

    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new MessagesResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/messages', $resource->fullPath());
        $this->assertSame('/v1/messages/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_finds_message_by_id(): void
    {
        $messageData = [
            'id'         => 123,
            'company_id' => 456,
            'driver_id'  => 789,
            'body'       => 'Test message',
            'direction'  => 'outbound',
        ];

        $response = $this->createMockResponse(['message' => $messageData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/messages/123')
            ->willReturn($response);

        $resource = new MessagesResource($client);
        $message = $resource->find(123);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame(123, $message->id);
        $this->assertSame('Test message', $message->body);
        $this->assertSame(MessageDirection::Outbound, $message->direction);
    }

    #[Test]
    public function it_gets_messages_for_driver(): void
    {
        $messagesData = [
            [
                'id'         => 1,
                'company_id' => 456,
                'driver_id'  => 789,
                'body'       => 'Message 1',
                'direction'  => 'inbound',
            ],
            [
                'id'         => 2,
                'company_id' => 456,
                'driver_id'  => 789,
                'body'       => 'Message 2',
                'direction'  => 'outbound',
            ],
        ];

        $response = $this->createMockResponse(['messages' => $messagesData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/messages/for_driver/789')
            ->willReturn($response);

        $resource = new MessagesResource($client);
        $messages = $resource->forDriver(789);

        $this->assertCount(2, $messages);
        $this->assertInstanceOf(Message::class, $messages->first());
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new MessagesResource($this->createStub(MotiveClient::class));

        $this->assertSame('messages', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new MessagesResource($this->createStub(MotiveClient::class));

        $this->assertSame('message', $resource->getResourceKey());
    }

    #[Test]
    public function it_sends_message(): void
    {
        $messageData = [
            'id'         => 789,
            'company_id' => 456,
            'driver_id'  => 123,
            'body'       => 'Hello driver!',
            'direction'  => 'outbound',
        ];

        $response = $this->createMockResponse(['message' => $messageData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/messages', ['message' => ['driver_id' => 123, 'body' => 'Hello driver!']])
            ->willReturn($response);

        $resource = new MessagesResource($client);
        $message = $resource->send([
            'driver_id' => 123,
            'body'      => 'Hello driver!',
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame('Hello driver!', $message->body);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}

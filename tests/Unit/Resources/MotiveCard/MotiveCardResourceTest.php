<?php

namespace Motive\Tests\Unit\Resources\MotiveCard;

use Motive\Data\CardLimit;
use Motive\Client\Response;
use Motive\Data\MotiveCard;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Data\CardTransaction;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\MotiveCard\MotiveCardResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveCardResourceTest extends TestCase
{
    private MotiveClient $client;

    private MotiveCardResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new MotiveCardResource($this->client);
    }

    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $this->assertSame('/v1/motive_cards', $this->resource->fullPath());
        $this->assertSame('/v1/motive_cards/123', $this->resource->fullPath('123'));
    }

    #[Test]
    public function it_finds_a_card(): void
    {
        $cardData = [
            'id'          => 123,
            'card_number' => '****1234',
            'driver_id'   => 456,
            'active'      => true,
        ];

        $response = $this->createMockResponse(['motive_card' => $cardData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/motive_cards/123')
            ->willReturn($response);

        $card = $this->resource->find(123);

        $this->assertInstanceOf(MotiveCard::class, $card);
        $this->assertSame(123, $card->id);
    }

    #[Test]
    public function it_gets_card_limits(): void
    {
        $limitData = [
            'id'          => 1,
            'card_id'     => 123,
            'daily_limit' => 500.00,
            'fuel_only'   => true,
        ];

        $response = $this->createMockResponse(['card_limit' => $limitData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/motive_cards/123/limits')
            ->willReturn($response);

        $limit = $this->resource->limits(123);

        $this->assertInstanceOf(CardLimit::class, $limit);
        $this->assertSame(500.00, $limit->dailyLimit);
        $this->assertTrue($limit->fuelOnly);
    }

    #[Test]
    public function it_gets_card_transactions(): void
    {
        $transactionsData = [
            [
                'id'               => 1,
                'card_id'          => 123,
                'amount'           => 75.50,
                'transaction_type' => 'fuel',
            ],
            [
                'id'               => 2,
                'card_id'          => 123,
                'amount'           => 25.00,
                'transaction_type' => 'toll',
            ],
        ];

        $response = $this->createMockResponse([
            'card_transactions' => $transactionsData,
            'pagination'        => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/motive_cards/123/transactions', ['page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $transactions = $this->resource->transactions(123);

        $this->assertInstanceOf(LazyCollection::class, $transactions);
        $transactionsArray = $transactions->all();
        $this->assertCount(2, $transactionsArray);
        $this->assertInstanceOf(CardTransaction::class, $transactionsArray[0]);
        $this->assertSame(75.50, $transactionsArray[0]->amount);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('motive_cards', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('motive_card', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_lists_cards(): void
    {
        $cardsData = [
            [
                'id'          => 1,
                'card_number' => '****1234',
                'active'      => true,
            ],
            [
                'id'          => 2,
                'card_number' => '****5678',
                'active'      => true,
            ],
        ];

        $response = $this->createMockResponse([
            'motive_cards' => $cardsData,
            'pagination'   => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $this->client->method('get')->willReturn($response);

        $cards = $this->resource->list();

        $this->assertInstanceOf(LazyCollection::class, $cards);

        $cardsArray = $cards->all();
        $this->assertCount(2, $cardsArray);
        $this->assertInstanceOf(MotiveCard::class, $cardsArray[0]);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}

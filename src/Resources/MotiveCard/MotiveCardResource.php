<?php

namespace Motive\Resources\MotiveCard;

use Motive\Data\CardLimit;
use Motive\Data\MotiveCard;
use Motive\Resources\Resource;
use Motive\Data\CardTransaction;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing Motive cards.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveCardResource extends Resource
{
    /**
     * Find a Motive card by ID.
     */
    public function find(int|string $id): MotiveCard
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return MotiveCard::from($response->json($this->resourceKey()));
    }

    /**
     * Get the limits for a card.
     */
    public function limits(int|string $cardId): CardLimit
    {
        $response = $this->client->get($this->fullPath("{$cardId}/limits"));

        return CardLimit::from($response->json('card_limit'));
    }

    /**
     * List all Motive cards.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, MotiveCard>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => MotiveCard::from($item));
    }

    /**
     * Get transactions for a card.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, CardTransaction>
     */
    public function transactions(int|string $cardId, array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath("{$cardId}/transactions"),
            resourceKey: 'card_transactions',
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => CardTransaction::from($item));
    }

    protected function basePath(): string
    {
        return 'motive_cards';
    }

    /**
     * @return class-string<MotiveCard>
     */
    protected function dtoClass(): string
    {
        return MotiveCard::class;
    }

    protected function resourceKey(): string
    {
        return 'motive_card';
    }
}

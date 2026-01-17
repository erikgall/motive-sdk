<?php

namespace Motive\Resources\Messages;

use Motive\Data\Message;
use Motive\Resources\Resource;
use Illuminate\Support\Collection;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing messages.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MessagesResource extends Resource
{
    /**
     * Broadcast a message to multiple drivers.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, Message>
     */
    public function broadcast(array $data): Collection
    {
        $response = $this->client->post($this->fullPath('broadcast'), [
            $this->resourceKey() => $data,
        ]);

        $items = $response->json($this->getPluralResourceKey()) ?? [];

        return collect(array_map(fn (array $item) => Message::from($item), $items));
    }

    /**
     * Find a message by ID.
     */
    public function find(int|string $id): Message
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return Message::from($response->json($this->resourceKey()));
    }

    /**
     * Get messages for a specific driver.
     *
     * @return Collection<int, Message>
     */
    public function forDriver(int|string $driverId): Collection
    {
        $response = $this->client->get($this->fullPath("for_driver/{$driverId}"));
        $data = $response->json($this->getPluralResourceKey()) ?? [];

        return collect(array_map(fn (array $item) => Message::from($item), $data));
    }

    /**
     * List all messages.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Message>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Message::from($item));
    }

    /**
     * Send a message to a driver.
     *
     * @param  array<string, mixed>  $data
     */
    public function send(array $data): Message
    {
        $response = $this->client->post($this->fullPath(), [
            $this->resourceKey() => $data,
        ]);

        return Message::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'messages';
    }

    /**
     * @return class-string<Message>
     */
    protected function dtoClass(): string
    {
        return Message::class;
    }

    protected function resourceKey(): string
    {
        return 'message';
    }
}

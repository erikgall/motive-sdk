<?php

namespace Motive\Resources\Dispatches;

use Motive\Data\Dispatch;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing dispatches.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchesResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get dispatches by status.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, Dispatch>
     */
    public function byStatus(string $status, array $params = []): array
    {
        $response = $this->client->get($this->fullPath("status/{$status}"), $params);
        $data = $response->json($this->getPluralResourceKey()) ?? [];

        return array_map(fn (array $item) => Dispatch::from($item), $data);
    }

    /**
     * List all dispatches.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Dispatch>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Dispatch::from($item));
    }

    protected function basePath(): string
    {
        return 'dispatches';
    }

    /**
     * @return class-string<Dispatch>
     */
    protected function dtoClass(): string
    {
        return Dispatch::class;
    }

    protected function resourceKey(): string
    {
        return 'dispatch';
    }
}

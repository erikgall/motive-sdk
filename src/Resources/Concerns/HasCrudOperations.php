<?php

namespace Motive\Resources\Concerns;

use Motive\Pagination\Paginator;
use Motive\Data\DataTransferObject;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Pagination\PaginatedResponse;

/**
 * Provides CRUD operations for resources.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
trait HasCrudOperations
{
    /**
     * Create a new resource.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): DataTransferObject
    {
        $response = $this->client->post($this->fullPath(), [$this->resourceKey() => $data]);
        $responseData = $response->json($this->resourceKey());
        $dtoClass = $this->dtoClass();

        return $dtoClass::from($responseData);
    }

    /**
     * Delete a resource.
     */
    public function delete(int|string $id): bool
    {
        $response = $this->client->delete($this->fullPath((string) $id));

        return $response->successful();
    }

    /**
     * Find a resource by ID.
     */
    public function find(int|string $id): DataTransferObject
    {
        $response = $this->client->get($this->fullPath((string) $id));
        $data = $response->json($this->resourceKey());
        $dtoClass = $this->dtoClass();

        return $dtoClass::from($data);
    }

    /**
     * List all resources with lazy pagination.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, DataTransferObject>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        $dtoClass = $this->dtoClass();

        return $lazyPaginator->cursor()->map(fn (array $item) => $dtoClass::from($item));
    }

    /**
     * Get a paginated response.
     *
     * @param  array<string, mixed>  $params
     * @return PaginatedResponse<DataTransferObject>
     */
    public function paginate(int $page = 1, int $perPage = 25, array $params = []): PaginatedResponse
    {
        $paginator = new Paginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey()
        );

        $response = $paginator->paginate($page, $perPage, $params);
        $dtoClass = $this->dtoClass();

        $items = $response->items()->map(fn (array $item) => $dtoClass::from($item));

        return new PaginatedResponse(
            items: $items,
            total: $response->total(),
            perPage: $response->perPage(),
            currentPage: $response->currentPage()
        );
    }

    /**
     * Update an existing resource.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int|string $id, array $data): DataTransferObject
    {
        $response = $this->client->patch($this->fullPath((string) $id), [$this->resourceKey() => $data]);
        $responseData = $response->json($this->resourceKey());
        $dtoClass = $this->dtoClass();

        return $dtoClass::from($responseData);
    }
}

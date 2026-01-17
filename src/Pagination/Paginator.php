<?php

namespace Motive\Pagination;

use Motive\Client\MotiveClient;
use Illuminate\Support\Collection;

/**
 * Handles API pagination for a single page request.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Paginator
{
    public function __construct(
        protected MotiveClient $client,
        protected string $path,
        protected string $resourceKey,
        protected int $defaultPerPage = 25
    ) {}

    /**
     * Get the path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the resource key.
     */
    public function getResourceKey(): string
    {
        return $this->resourceKey;
    }

    /**
     * Fetch a specific page of results.
     *
     * @param  array<string, mixed>  $params
     * @return PaginatedResponse<array<string, mixed>>
     */
    public function paginate(int $page = 1, int $perPage = 25, array $params = []): PaginatedResponse
    {
        $query = array_merge($params, [
            'page_no'  => $page,
            'per_page' => $perPage,
        ]);

        $response = $this->client->get($this->path, $query);

        $items = $response->json($this->resourceKey) ?? [];
        $pagination = $response->json('pagination') ?? [];

        return new PaginatedResponse(
            items: new Collection($items),
            total: $pagination['total'] ?? count($items),
            perPage: $pagination['per_page'] ?? $perPage,
            currentPage: $pagination['current_page'] ?? $page
        );
    }
}

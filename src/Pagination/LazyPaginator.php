<?php

namespace Motive\Pagination;

use Generator;
use Motive\Client\MotiveClient;
use Illuminate\Support\LazyCollection;

/**
 * Memory-efficient paginator using generators.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class LazyPaginator
{
    public function __construct(
        protected MotiveClient $client,
        protected string $path,
        protected string $resourceKey,
        protected int $perPage = 25,
        /** @var array<string, mixed> */
        protected array $params = []
    ) {}

    /**
     * Get a lazy collection that iterates through all pages.
     *
     * @return LazyCollection<int, array<string, mixed>>
     */
    public function cursor(): LazyCollection
    {
        return new LazyCollection(function () {
            yield from $this->fetchAllPages();
        });
    }

    /**
     * Set the number of items per page.
     */
    public function perPage(int $perPage): static
    {
        $clone = clone $this;
        $clone->perPage = $perPage;

        return $clone;
    }

    /**
     * Set additional query parameters.
     *
     * @param  array<string, mixed>  $params
     */
    public function withParams(array $params): static
    {
        $clone = clone $this;
        $clone->params = array_merge($clone->params, $params);

        return $clone;
    }

    /**
     * Generator that fetches all pages sequentially.
     *
     * @return Generator<int, array<string, mixed>>
     */
    protected function fetchAllPages(): Generator
    {
        $page = 1;
        $lastPage = 1;

        do {
            $query = array_merge($this->params, [
                'page_no'  => $page,
                'per_page' => $this->perPage,
            ]);

            $response = $this->client->get($this->path, $query);

            $items = $response->json($this->resourceKey) ?? [];
            $pagination = $response->json('pagination') ?? [];

            foreach ($items as $item) {
                yield $item;
            }

            $total = $pagination['total'] ?? count($items);
            $perPage = $pagination['per_page'] ?? $this->perPage;
            $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

            $page++;
        } while ($page <= $lastPage && ! empty($items));
    }
}

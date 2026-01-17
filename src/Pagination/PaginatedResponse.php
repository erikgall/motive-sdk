<?php

namespace Motive\Pagination;

use Countable;
use Traversable;
use ArrayIterator;
use IteratorAggregate;
use Illuminate\Support\Collection;

/**
 * Wrapper for paginated API responses with metadata.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @template TItem
 *
 * @implements IteratorAggregate<int, TItem>
 */
class PaginatedResponse implements Countable, IteratorAggregate
{
    /**
     * @param  Collection<int, TItem>  $items
     */
    public function __construct(
        protected Collection $items,
        protected int $total,
        protected int $perPage,
        protected int $currentPage
    ) {}

    /**
     * Get the number of items on the current page.
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get the current page number.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get an iterator for the items.
     *
     * @return Traversable<int, TItem>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items->all());
    }

    /**
     * Determine if there are more pages.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    /**
     * Get the items for the current page.
     *
     * @return Collection<int, TItem>
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * Get the last page number.
     */
    public function lastPage(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Get the number of items per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the total number of items.
     */
    public function total(): int
    {
        return $this->total;
    }
}

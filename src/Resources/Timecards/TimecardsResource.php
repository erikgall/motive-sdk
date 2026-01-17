<?php

namespace Motive\Resources\Timecards;

use Motive\Data\Timecard;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing timecards.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class TimecardsResource extends Resource
{
    /**
     * Find a timecard by ID.
     */
    public function find(int|string $id): Timecard
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return Timecard::from($response->json($this->resourceKey()));
    }

    /**
     * Get timecards for a specific driver.
     *
     * @return LazyCollection<int, Timecard>
     */
    public function forDriver(int|string $driverId): LazyCollection
    {
        return $this->list(['driver_id' => $driverId]);
    }

    /**
     * List all timecards.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Timecard>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Timecard::from($item));
    }

    /**
     * Update a timecard.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int|string $id, array $data): Timecard
    {
        $response = $this->client->patch($this->fullPath((string) $id), [
            $this->resourceKey() => $data,
        ]);

        return Timecard::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'timecards';
    }

    /**
     * @return class-string<Timecard>
     */
    protected function dtoClass(): string
    {
        return Timecard::class;
    }

    protected function resourceKey(): string
    {
        return 'timecard';
    }
}

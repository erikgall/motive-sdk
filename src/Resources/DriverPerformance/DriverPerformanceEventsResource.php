<?php

namespace Motive\Resources\DriverPerformance;

use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Data\DriverPerformanceEvent;

/**
 * Resource for managing driver performance events.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DriverPerformanceEventsResource extends Resource
{
    /**
     * Find a performance event by ID.
     */
    public function find(int|string $id): DriverPerformanceEvent
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return DriverPerformanceEvent::from($response->json($this->resourceKey()));
    }

    /**
     * Get performance events within a date range.
     *
     * @return LazyCollection<int, DriverPerformanceEvent>
     */
    public function forDateRange(string $startDate, string $endDate): LazyCollection
    {
        return $this->list([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    /**
     * Get performance events for a specific driver.
     *
     * @return LazyCollection<int, DriverPerformanceEvent>
     */
    public function forDriver(int|string $driverId): LazyCollection
    {
        return $this->list(['driver_id' => $driverId]);
    }

    /**
     * List all performance events.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, DriverPerformanceEvent>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => DriverPerformanceEvent::from($item));
    }

    protected function basePath(): string
    {
        return 'driver_performance_events';
    }

    /**
     * @return class-string<DriverPerformanceEvent>
     */
    protected function dtoClass(): string
    {
        return DriverPerformanceEvent::class;
    }

    protected function resourceKey(): string
    {
        return 'driver_performance_event';
    }
}

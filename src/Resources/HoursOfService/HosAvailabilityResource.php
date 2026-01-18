<?php

namespace Motive\Resources\HoursOfService;

use Motive\Resources\Resource;
use Motive\Data\HosAvailability;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing Hours of Service availability.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosAvailabilityResource extends Resource
{
    /**
     * Get HOS availability for a specific driver.
     */
    public function forDriver(int|string $driverId): HosAvailability
    {
        $response = $this->client->get($this->fullPath("driver/{$driverId}"));
        $data = $response->json($this->resourceKey());

        return HosAvailability::from($data);
    }

    /**
     * List all HOS availability records.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, HosAvailability>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => HosAvailability::from($item));
    }

    protected function basePath(): string
    {
        return 'hos_availability';
    }

    /**
     * @return class-string<HosAvailability>
     */
    protected function dtoClass(): string
    {
        return HosAvailability::class;
    }

    protected function resourceKey(): string
    {
        return 'hos_availability';
    }
}

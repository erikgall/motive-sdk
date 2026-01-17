<?php

namespace Motive\Resources\Locations;

use Motive\Data\Location;
use Motive\Resources\Resource;
use Illuminate\Support\Collection;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing locations.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class LocationsResource extends Resource
{
    use HasCrudOperations;

    /**
     * Find nearest locations to a given coordinate.
     *
     * @return Collection<int, Location>
     */
    public function findNearest(float $latitude, float $longitude, int $radius = 1000): Collection
    {
        $response = $this->client->get($this->fullPath('nearest'), [
            'latitude'  => $latitude,
            'longitude' => $longitude,
            'radius'    => $radius,
        ]);

        $data = $response->json($this->getPluralResourceKey()) ?? [];

        return collect(array_map(fn (array $item) => Location::from($item), $data));
    }

    /**
     * List all locations.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Location>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Location::from($item));
    }

    protected function basePath(): string
    {
        return 'locations';
    }

    /**
     * @return class-string<Location>
     */
    protected function dtoClass(): string
    {
        return Location::class;
    }

    protected function resourceKey(): string
    {
        return 'location';
    }
}

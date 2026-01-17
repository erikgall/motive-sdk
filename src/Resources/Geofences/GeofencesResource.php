<?php

namespace Motive\Resources\Geofences;

use Motive\Data\Geofence;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing geofences.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofencesResource extends Resource
{
    use HasCrudOperations;

    /**
     * List all geofences.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Geofence>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Geofence::from($item));
    }

    protected function basePath(): string
    {
        return 'geofences';
    }

    /**
     * @return class-string<Geofence>
     */
    protected function dtoClass(): string
    {
        return Geofence::class;
    }

    protected function resourceKey(): string
    {
        return 'geofence';
    }
}

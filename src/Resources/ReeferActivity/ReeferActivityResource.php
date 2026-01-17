<?php

namespace Motive\Resources\ReeferActivity;

use Motive\Resources\Resource;
use Motive\Data\ReeferActivity;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing reefer activity data.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ReeferActivityResource extends Resource
{
    /**
     * Get reefer activity for a specific vehicle.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, ReeferActivity>
     */
    public function forVehicle(int|string $vehicleId, array $params = []): LazyCollection
    {
        return $this->list(array_merge($params, ['vehicle_id' => $vehicleId]));
    }

    /**
     * List all reefer activity.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, ReeferActivity>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => ReeferActivity::from($item));
    }

    protected function basePath(): string
    {
        return 'reefer_activities';
    }

    /**
     * @return class-string<ReeferActivity>
     */
    protected function dtoClass(): string
    {
        return ReeferActivity::class;
    }

    protected function resourceKey(): string
    {
        return 'reefer_activity';
    }
}

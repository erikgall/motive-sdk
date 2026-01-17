<?php

namespace Motive\Resources\VehicleGateways;

use Motive\Resources\Resource;
use Motive\Data\VehicleGateway;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing vehicle gateways.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleGatewaysResource extends Resource
{
    /**
     * Find a vehicle gateway by ID.
     */
    public function find(int|string $id): VehicleGateway
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return VehicleGateway::from($response->json($this->resourceKey()));
    }

    /**
     * List all vehicle gateways.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, VehicleGateway>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => VehicleGateway::from($item));
    }

    protected function basePath(): string
    {
        return 'vehicle_gateways';
    }

    /**
     * @return class-string<VehicleGateway>
     */
    protected function dtoClass(): string
    {
        return VehicleGateway::class;
    }

    protected function resourceKey(): string
    {
        return 'vehicle_gateway';
    }
}

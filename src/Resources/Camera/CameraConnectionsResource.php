<?php

namespace Motive\Resources\Camera;

use Motive\Resources\Resource;
use Motive\Data\CameraConnection;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing camera connections.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class CameraConnectionsResource extends Resource
{
    /**
     * Find a camera connection by ID.
     */
    public function find(int|string $id): CameraConnection
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return CameraConnection::from($response->json($this->resourceKey()));
    }

    /**
     * Get camera connections for a specific vehicle.
     *
     * @return LazyCollection<int, CameraConnection>
     */
    public function forVehicle(int|string $vehicleId): LazyCollection
    {
        return $this->list(['vehicle_id' => $vehicleId]);
    }

    /**
     * List all camera connections.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, CameraConnection>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => CameraConnection::from($item));
    }

    protected function basePath(): string
    {
        return 'camera_connections';
    }

    /**
     * @return class-string<CameraConnection>
     */
    protected function dtoClass(): string
    {
        return CameraConnection::class;
    }

    protected function resourceKey(): string
    {
        return 'camera_connection';
    }
}

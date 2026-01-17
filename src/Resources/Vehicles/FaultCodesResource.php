<?php

namespace Motive\Resources\Vehicles;

use Motive\Data\FaultCode;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing vehicle fault codes.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FaultCodesResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get fault codes for a specific vehicle.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, FaultCode>
     */
    public function forVehicle(int|string $vehicleId, array $params = []): array
    {
        $response = $this->client->get($this->fullPath("vehicle/{$vehicleId}"), $params);
        $data = $response->json($this->getPluralResourceKey()) ?? [];

        return array_map(fn (array $item) => FaultCode::from($item), $data);
    }

    /**
     * List all fault codes.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, FaultCode>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => FaultCode::from($item));
    }

    /**
     * Mark a fault code as resolved.
     */
    public function resolve(int|string $id): FaultCode
    {
        $response = $this->client->post($this->fullPath("{$id}/resolve"), []);
        $data = $response->json($this->resourceKey());

        return FaultCode::from($data);
    }

    protected function basePath(): string
    {
        return 'fault_codes';
    }

    /**
     * @return class-string<FaultCode>
     */
    protected function dtoClass(): string
    {
        return FaultCode::class;
    }

    protected function resourceKey(): string
    {
        return 'fault_code';
    }
}

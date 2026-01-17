<?php

namespace Motive\Resources\Vehicles;

use Motive\Data\Vehicle;
use Motive\Resources\Resource;
use Motive\Data\VehicleLocation;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing vehicles.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehiclesResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get the current location of a vehicle.
     */
    public function currentLocation(int|string $id): VehicleLocation
    {
        $response = $this->client->get($this->fullPath("{$id}/current_location"));
        $data = $response->json('vehicle_location');

        return VehicleLocation::from($data);
    }

    /**
     * Find a vehicle by its number.
     */
    public function findByNumber(string $number): Vehicle
    {
        $response = $this->client->get($this->fullPath("by_number/{$number}"));
        $data = $response->json($this->resourceKey());

        return Vehicle::from($data);
    }

    /**
     * Get location history for a vehicle.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, VehicleLocation>
     */
    public function locations(int|string $id, array $params = []): LazyCollection
    {
        return LazyCollection::make(function () use ($id, $params) {
            $response = $this->client->get($this->fullPath("{$id}/locations"), $params);
            $locations = $response->json('vehicle_locations') ?? [];

            foreach ($locations as $location) {
                yield VehicleLocation::from($location);
            }
        });
    }

    protected function basePath(): string
    {
        return 'vehicles';
    }

    /**
     * @return class-string<Vehicle>
     */
    protected function dtoClass(): string
    {
        return Vehicle::class;
    }

    protected function resourceKey(): string
    {
        return 'vehicle';
    }
}

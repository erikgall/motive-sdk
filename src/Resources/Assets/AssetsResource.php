<?php

namespace Motive\Resources\Assets;

use Motive\Data\Asset;
use Motive\Resources\Resource;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing assets.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class AssetsResource extends Resource
{
    use HasCrudOperations;

    /**
     * Assign an asset to a vehicle.
     */
    public function assignToVehicle(int|string $assetId, int|string $vehicleId): bool
    {
        $response = $this->client->post($this->fullPath("{$assetId}/assign"), [
            'vehicle_id' => $vehicleId,
        ]);

        return $response->successful();
    }

    /**
     * Unassign an asset from its current vehicle.
     */
    public function unassignFromVehicle(int|string $assetId): bool
    {
        $response = $this->client->post($this->fullPath("{$assetId}/unassign"));

        return $response->successful();
    }

    protected function basePath(): string
    {
        return 'assets';
    }

    /**
     * @return class-string<Asset>
     */
    protected function dtoClass(): string
    {
        return Asset::class;
    }

    protected function resourceKey(): string
    {
        return 'asset';
    }
}

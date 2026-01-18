<?php

namespace Motive\Testing\Factories;

use Motive\Data\Asset;

/**
 * Factory for creating Asset test data.
 *
 * @extends Factory<Asset>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class AssetFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $assetTypes = ['trailer', 'container', 'chassis', 'equipment'];

    /**
     * Assign to a vehicle.
     */
    public function assignedTo(int $vehicleId): static
    {
        return $this->state(['vehicle_id' => $vehicleId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'         => $id,
            'company_id' => 1,
            'name'       => 'Asset-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT),
            'asset_type' => static::$assetTypes[array_rand(static::$assetTypes)],
            'status'     => 'active',
            'vehicle_id' => null,
        ];
    }

    /**
     * @return class-string<Asset>
     */
    public function dtoClass(): string
    {
        return Asset::class;
    }

    /**
     * Set the asset as inactive.
     */
    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    /**
     * Set the asset type to trailer.
     */
    public function trailer(): static
    {
        return $this->state(['asset_type' => 'trailer']);
    }
}

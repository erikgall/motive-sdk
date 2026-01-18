<?php

namespace Motive\Testing\Factories;

use Motive\Data\Geofence;

/**
 * Factory for creating Geofence test data.
 *
 * @extends Factory<Geofence>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class GeofenceFactory extends Factory
{
    /**
     * Set as circle type.
     */
    public function circle(float $latitude, float $longitude, int $radius): static
    {
        return $this->state([
            'geofence_type' => 'circle',
            'latitude'      => $latitude,
            'longitude'     => $longitude,
            'radius'        => $radius,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'            => $id,
            'company_id'    => 1,
            'name'          => 'Geofence-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT),
            'geofence_type' => 'circle',
            'latitude'      => 37.7749 + (rand(-100, 100) / 1000),
            'longitude'     => -122.4194 + (rand(-100, 100) / 1000),
            'radius'        => rand(100, 5000),
            'coordinates'   => [],
        ];
    }

    /**
     * @return class-string<Geofence>
     */
    public function dtoClass(): string
    {
        return Geofence::class;
    }

    /**
     * Set as polygon type.
     *
     * @param  array<int, array{latitude: float, longitude: float}>  $coordinates
     */
    public function polygon(array $coordinates): static
    {
        return $this->state([
            'geofence_type' => 'polygon',
            'coordinates'   => $coordinates,
            'latitude'      => null,
            'longitude'     => null,
            'radius'        => null,
        ]);
    }
}

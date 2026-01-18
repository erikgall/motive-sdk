<?php

namespace Motive\Testing\Factories;

use Motive\Data\Location;

/**
 * Factory for creating Location test data.
 *
 * @extends Factory<Location>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class LocationFactory extends Factory
{
    /**
     * @var array<int, array{name: string, lat: float, lng: float}>
     */
    protected static array $locations = [
        ['name' => 'Los Angeles Distribution Center', 'lat' => 34.0522, 'lng' => -118.2437],
        ['name' => 'Chicago Warehouse', 'lat' => 41.8781, 'lng' => -87.6298],
        ['name' => 'New York Terminal', 'lat' => 40.7128, 'lng' => -74.0060],
        ['name' => 'Dallas Hub', 'lat' => 32.7767, 'lng' => -96.7970],
        ['name' => 'Atlanta Depot', 'lat' => 33.7490, 'lng' => -84.3880],
    ];

    /**
     * Set specific coordinates.
     */
    public function at(float $latitude, float $longitude): static
    {
        return $this->state([
            'latitude'  => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();
        $location = static::$locations[array_rand(static::$locations)];

        return [
            'id'         => $id,
            'company_id' => 1,
            'name'       => $location['name'].' #'.$id,
            'address'    => '123 Main Street',
            'city'       => 'Test City',
            'state'      => 'CA',
            'zip'        => '90210',
            'country'    => 'US',
            'latitude'   => $location['lat'] + (rand(-100, 100) / 10000),
            'longitude'  => $location['lng'] + (rand(-100, 100) / 10000),
        ];
    }

    /**
     * @return class-string<Location>
     */
    public function dtoClass(): string
    {
        return Location::class;
    }

    /**
     * Set address.
     */
    public function withAddress(string $address, string $city, string $state, string $zip): static
    {
        return $this->state([
            'address' => $address,
            'city'    => $city,
            'state'   => $state,
            'zip'     => $zip,
        ]);
    }
}

<?php

namespace Motive\Testing\Factories;

use Motive\Data\Vehicle;

/**
 * Factory for creating Vehicle test data.
 *
 * @extends Factory<Vehicle>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VehicleFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $makes = ['Freightliner', 'Peterbilt', 'Kenworth', 'Volvo', 'Mack', 'International'];

    /**
     * @var array<int, string>
     */
    protected static array $models = ['Cascadia', '579', 'T680', 'VNL', 'Anthem', 'LT'];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'         => $id,
            'company_id' => 1,
            'number'     => 'V-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT),
            'make'       => static::$makes[array_rand(static::$makes)],
            'model'      => static::$models[array_rand(static::$models)],
            'year'       => rand(2018, 2024),
            'vin'        => $this->generateVin(),
            'status'     => 'active',
        ];
    }

    /**
     * @return class-string<Vehicle>
     */
    public function dtoClass(): string
    {
        return Vehicle::class;
    }

    /**
     * Set the vehicle as inactive.
     */
    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    /**
     * Set a custom VIN.
     */
    public function withVin(string $vin): static
    {
        return $this->state(['vin' => $vin]);
    }

    /**
     * Generate a random VIN-like string.
     */
    protected function generateVin(): string
    {
        $chars = '0123456789ABCDEFGHJKLMNPRSTUVWXYZ';
        $vin = '';

        for ($i = 0; $i < 17; $i++) {
            $vin .= $chars[rand(0, strlen($chars) - 1)];
        }

        return $vin;
    }
}

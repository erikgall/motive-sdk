<?php

namespace Motive\Testing\Factories;

use Motive\Data\FuelPurchase;

/**
 * Factory for creating FuelPurchase test data.
 *
 * @extends Factory<FuelPurchase>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FuelPurchaseFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $fuelTypes = ['diesel', 'regular', 'premium', 'def'];

    /**
     * @var array<int, string>
     */
    protected static array $vendors = ['Pilot', 'Flying J', 'Love\'s', 'TA', 'Petro'];

    /**
     * Set as DEF purchase.
     */
    public function def(): static
    {
        return $this->state(['fuel_type' => 'def']);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();
        $gallons = rand(50, 200) + (rand(0, 99) / 100);
        $pricePerGallon = 3 + (rand(0, 150) / 100);

        return [
            'id'           => $id,
            'company_id'   => 1,
            'driver_id'    => rand(1, 100),
            'vehicle_id'   => rand(1, 100),
            'fuel_type'    => static::$fuelTypes[array_rand(static::$fuelTypes)],
            'quantity'     => round($gallons, 2),
            'total_cost'   => round($gallons * $pricePerGallon, 2),
            'unit_price'   => round($pricePerGallon, 3),
            'vendor_name'  => static::$vendors[array_rand(static::$vendors)],
            'odometer'     => rand(100000, 500000),
            'purchased_at' => date('Y-m-d\TH:i:s\Z'),
            'created_at'   => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * Set as diesel purchase.
     */
    public function diesel(): static
    {
        return $this->state(['fuel_type' => 'diesel']);
    }

    /**
     * @return class-string<FuelPurchase>
     */
    public function dtoClass(): string
    {
        return FuelPurchase::class;
    }

    /**
     * Set the driver.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }

    /**
     * Set specific gallons and cost.
     */
    public function withPurchase(float $gallons, float $pricePerGallon): static
    {
        return $this->state([
            'quantity'   => $gallons,
            'unit_price' => $pricePerGallon,
            'total_cost' => round($gallons * $pricePerGallon, 2),
        ]);
    }

    /**
     * Set the vehicle.
     */
    public function withVehicle(int $vehicleId): static
    {
        return $this->state(['vehicle_id' => $vehicleId]);
    }
}

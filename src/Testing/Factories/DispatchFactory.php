<?php

namespace Motive\Testing\Factories;

use Motive\Data\Dispatch;

/**
 * Factory for creating Dispatch test data.
 *
 * @extends Factory<Dispatch>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchFactory extends Factory
{
    /**
     * Set as cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    /**
     * Set as completed.
     */
    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'          => $id,
            'company_id'  => 1,
            'external_id' => 'DISP-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT),
            'status'      => 'pending',
            'driver_id'   => rand(1, 100),
            'vehicle_id'  => rand(1, 100),
            'stops'       => [],
            'created_at'  => date('Y-m-d\TH:i:s\Z'),
            'updated_at'  => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * @return class-string<Dispatch>
     */
    public function dtoClass(): string
    {
        return Dispatch::class;
    }

    /**
     * Set as in progress.
     */
    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }

    /**
     * Set the driver.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }

    /**
     * Add stops to the dispatch.
     *
     * @param  array<int, array<string, mixed>>  $stops
     */
    public function withStops(array $stops): static
    {
        return $this->state(['stops' => $stops]);
    }

    /**
     * Set the vehicle.
     */
    public function withVehicle(int $vehicleId): static
    {
        return $this->state(['vehicle_id' => $vehicleId]);
    }
}

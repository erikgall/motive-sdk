<?php

namespace Motive\Testing\Factories;

use Motive\Data\InspectionReport;

/**
 * Factory for creating InspectionReport test data.
 *
 * @extends Factory<InspectionReport>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'              => $id,
            'driver_id'       => rand(1, 100),
            'vehicle_id'      => rand(1, 100),
            'inspection_type' => 'pre_trip',
            'status'          => 'satisfactory',
            'started_at'      => date('Y-m-d\TH:i:s\Z'),
            'location'        => 'Test Location, CA',
            'odometer'        => rand(10000, 500000),
            'defects'         => [],
            'completed_at'    => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * @return class-string<InspectionReport>
     */
    public function dtoClass(): string
    {
        return InspectionReport::class;
    }

    /**
     * Set as failed with defects.
     *
     * @param  array<int, array<string, mixed>>  $defects
     */
    public function failed(array $defects = []): static
    {
        if (empty($defects)) {
            $defects = [
                [
                    'id'            => 1,
                    'inspection_id' => 1,
                    'category'      => 'tires',
                    'description'   => 'Low tire pressure',
                    'severity'      => 'minor',
                ],
            ];
        }

        return $this->state([
            'status'  => 'failed',
            'defects' => $defects,
        ]);
    }

    /**
     * Set as post-trip inspection.
     */
    public function postTrip(): static
    {
        return $this->state(['inspection_type' => 'post_trip']);
    }

    /**
     * Set as pre-trip inspection.
     */
    public function preTrip(): static
    {
        return $this->state(['inspection_type' => 'pre_trip']);
    }

    /**
     * Set as satisfactory (no defects).
     */
    public function satisfactory(): static
    {
        return $this->state([
            'status'  => 'satisfactory',
            'defects' => [],
        ]);
    }

    /**
     * Set the driver.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }

    /**
     * Set the vehicle.
     */
    public function withVehicle(int $vehicleId): static
    {
        return $this->state(['vehicle_id' => $vehicleId]);
    }
}

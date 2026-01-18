<?php

namespace Motive\Testing\Factories;

use Motive\Data\HosAvailability;

/**
 * Factory for creating HosAvailability test data.
 *
 * @extends Factory<HosAvailability>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosAvailabilityFactory extends Factory
{
    /**
     * Set as break required.
     */
    public function breakRequired(): static
    {
        return $this->state(['break_time_required' => 30]);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'                   => $id,
            'driver_id'            => rand(1, 100),
            'drive_time_remaining' => rand(0, 660), // 0-11 hours in minutes
            'shift_time_remaining' => rand(0, 840), // 0-14 hours in minutes
            'cycle_time_remaining' => rand(0, 4200), // 0-70 hours in minutes
            'break_time_required'  => rand(0, 30),
            'date'                 => date('Y-m-d'),
        ];
    }

    /**
     * @return class-string<HosAvailability>
     */
    public function dtoClass(): string
    {
        return HosAvailability::class;
    }

    /**
     * Set as out of cycle time.
     */
    public function outOfCycleTime(): static
    {
        return $this->state(['cycle_time_remaining' => 0]);
    }

    /**
     * Set as out of drive time.
     */
    public function outOfDriveTime(): static
    {
        return $this->state(['drive_time_remaining' => 0]);
    }

    /**
     * Set the driver ID.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }

    /**
     * Set full availability.
     */
    public function withFullAvailability(): static
    {
        return $this->state([
            'drive_time_remaining' => 660,
            'shift_time_remaining' => 840,
            'cycle_time_remaining' => 4200,
            'break_time_required'  => 0,
        ]);
    }
}

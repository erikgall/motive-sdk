<?php

namespace Motive\Testing\Factories;

use Motive\Data\HosLog;

/**
 * Factory for creating HosLog test data.
 *
 * @extends Factory<HosLog>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosLogFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $dutyStatuses = ['off_duty', 'sleeper_berth', 'driving', 'on_duty'];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'          => $id,
            'driver_id'   => rand(1, 100),
            'duty_status' => static::$dutyStatuses[array_rand(static::$dutyStatuses)],
            'start_time'  => date('Y-m-d\TH:i:s\Z', strtotime('-'.rand(1, 24).' hours')),
            'vehicle_id'  => rand(1, 100),
            'duration'    => rand(30, 480),
            'location'    => 'Test Location, CA',
            'latitude'    => 37.7749 + (rand(-100, 100) / 1000),
            'longitude'   => -122.4194 + (rand(-100, 100) / 1000),
            'annotation'  => null,
        ];
    }

    /**
     * Set the log as driving status.
     */
    public function driving(): static
    {
        return $this->state(['duty_status' => 'driving']);
    }

    /**
     * @return class-string<HosLog>
     */
    public function dtoClass(): string
    {
        return HosLog::class;
    }

    /**
     * Set the log as off duty status.
     */
    public function offDuty(): static
    {
        return $this->state(['duty_status' => 'off_duty']);
    }

    /**
     * Set the log as on duty status.
     */
    public function onDuty(): static
    {
        return $this->state(['duty_status' => 'on_duty']);
    }

    /**
     * Set the log as sleeper berth status.
     */
    public function sleeperBerth(): static
    {
        return $this->state(['duty_status' => 'sleeper_berth']);
    }

    /**
     * Add an annotation.
     */
    public function withAnnotation(string $annotation): static
    {
        return $this->state(['annotation' => $annotation]);
    }

    /**
     * Set the driver ID.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }
}

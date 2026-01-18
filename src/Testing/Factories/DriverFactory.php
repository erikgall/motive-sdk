<?php

namespace Motive\Testing\Factories;

use Motive\Data\Driver;

/**
 * Factory for creating Driver test data.
 *
 * @extends Factory<Driver>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DriverFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily'];

    /**
     * @var array<int, string>
     */
    protected static array $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Davis'];

    /**
     * @var array<int, string>
     */
    protected static array $licenseStates = ['CA', 'TX', 'FL', 'NY', 'IL', 'PA', 'OH'];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'                 => $id,
            'first_name'         => static::$firstNames[array_rand(static::$firstNames)],
            'last_name'          => static::$lastNames[array_rand(static::$lastNames)],
            'user_id'            => $id,
            'company_id'         => 1,
            'license_number'     => 'DL'.str_pad((string) $id, 8, '0', STR_PAD_LEFT),
            'license_state'      => static::$licenseStates[array_rand(static::$licenseStates)],
            'license_expiration' => date('Y-m-d', strtotime('+1 year')),
            'carrier_name'       => 'Test Carrier Inc.',
            'carrier_dot_number' => str_pad((string) rand(100000, 999999), 7, '0', STR_PAD_LEFT),
            'eld_exempt'         => false,
        ];
    }

    /**
     * @return class-string<Driver>
     */
    public function dtoClass(): string
    {
        return Driver::class;
    }

    /**
     * Set the driver as ELD exempt.
     */
    public function eldExempt(string $reason = 'Short haul exemption'): static
    {
        return $this->state([
            'eld_exempt' => true,
        ]);
    }

    /**
     * Set license to expired.
     */
    public function expiredLicense(): static
    {
        return $this->state([
            'license_expiration' => date('Y-m-d', strtotime('-1 month')),
        ]);
    }
}

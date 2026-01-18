<?php

namespace Motive\Testing\Factories;

use Motive\Data\User;

/**
 * Factory for creating User test data.
 *
 * @extends Factory<User>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class UserFactory extends Factory
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
     * Set the user as admin.
     */
    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    /**
     * Set the user as deactivated.
     */
    public function deactivated(): static
    {
        return $this->state(['status' => 'deactivated']);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();
        $firstName = static::$firstNames[array_rand(static::$firstNames)];
        $lastName = static::$lastNames[array_rand(static::$lastNames)];

        return [
            'id'         => $id,
            'company_id' => 1,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => strtolower($firstName).'.'.strtolower($lastName).$id.'@example.com',
            'phone'      => '555-'.str_pad((string) rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'role'       => 'driver',
            'status'     => 'active',
        ];
    }

    /**
     * Set the user as a driver.
     */
    public function driver(): static
    {
        return $this->state(['role' => 'driver']);
    }

    /**
     * @return class-string<User>
     */
    public function dtoClass(): string
    {
        return User::class;
    }
}

<?php

namespace Motive\Testing\Factories;

use Motive\Data\Message;

/**
 * Factory for creating Message test data.
 *
 * @extends Factory<Message>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MessageFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $bodies = [
        'Please confirm your arrival time.',
        'Load has been picked up.',
        'Delivery complete.',
        'Running 30 minutes behind schedule.',
        'Need fuel stop.',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'         => $id,
            'company_id' => 1,
            'driver_id'  => rand(1, 100),
            'body'       => static::$bodies[array_rand(static::$bodies)],
            'direction'  => 'outbound',
            'read'       => false,
            'created_at' => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * @return class-string<Message>
     */
    public function dtoClass(): string
    {
        return Message::class;
    }

    /**
     * Set as inbound message (from driver).
     */
    public function inbound(): static
    {
        return $this->state(['direction' => 'inbound']);
    }

    /**
     * Set as outbound message (to driver).
     */
    public function outbound(): static
    {
        return $this->state(['direction' => 'outbound']);
    }

    /**
     * Set as read.
     */
    public function read(): static
    {
        return $this->state(['read' => true]);
    }

    /**
     * Set as unread.
     */
    public function unread(): static
    {
        return $this->state(['read' => false]);
    }

    /**
     * Set the driver.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }
}

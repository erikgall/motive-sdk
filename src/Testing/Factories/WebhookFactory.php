<?php

namespace Motive\Testing\Factories;

use Motive\Data\Webhook;

/**
 * Factory for creating Webhook test data.
 *
 * @extends Factory<Webhook>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $eventTypes = [
        'vehicle.location_updated',
        'driver.hos_status_changed',
        'dispatch.created',
        'inspection.submitted',
        'driver.hos_violation',
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
            'url'        => 'https://example.com/webhooks/'.$id,
            'events'     => [static::$eventTypes[array_rand(static::$eventTypes)]],
            'secret'     => bin2hex(random_bytes(16)),
            'status'     => 'active',
            'created_at' => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * @return class-string<Webhook>
     */
    public function dtoClass(): string
    {
        return Webhook::class;
    }

    /**
     * Set as inactive.
     */
    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }

    /**
     * Subscribe to specific events.
     *
     * @param  array<int, string>  $events
     */
    public function subscribedTo(array $events): static
    {
        return $this->state(['events' => $events]);
    }

    /**
     * Set the webhook URL.
     */
    public function withUrl(string $url): static
    {
        return $this->state(['url' => $url]);
    }
}

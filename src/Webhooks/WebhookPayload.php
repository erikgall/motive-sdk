<?php

namespace Motive\Webhooks;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Motive\Enums\WebhookEvent;

/**
 * Webhook payload parser and accessor.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookPayload
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rawData
     */
    public function __construct(
        public readonly WebhookEvent $event,
        public readonly CarbonImmutable $timestamp,
        public readonly array $data,
        private readonly array $rawData
    ) {}

    /**
     * Get the raw payload data.
     *
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->rawData;
    }

    /**
     * Convert the payload to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event'     => $this->event->value,
            'timestamp' => $this->timestamp->toIso8601String(),
            'data'      => $this->data,
        ];
    }

    /**
     * Create a WebhookPayload from an array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            event: WebhookEvent::from($data['event']),
            timestamp: CarbonImmutable::parse($data['timestamp']),
            data: $data['data'] ?? [],
            rawData: $data
        );
    }

    /**
     * Create a WebhookPayload from an HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        $data = $request->json()->all();

        return self::from($data);
    }
}

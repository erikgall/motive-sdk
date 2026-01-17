<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\WebhookEvent;

/**
 * Webhook delivery log data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookLog extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $webhookId,
        public WebhookEvent $event,
        public int $statusCode,
        public ?string $responseBody = null,
        public ?string $requestBody = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Check if the webhook delivery was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'event' => WebhookEvent::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'webhook_id'    => 'webhookId',
            'status_code'   => 'statusCode',
            'response_body' => 'responseBody',
            'request_body'  => 'requestBody',
            'created_at'    => 'createdAt',
        ];
    }
}

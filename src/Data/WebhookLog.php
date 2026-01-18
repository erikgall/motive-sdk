<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\WebhookEvent;

/**
 * Webhook delivery log data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $webhookId
 * @property WebhookEvent $event
 * @property int $statusCode
 * @property string|null $responseBody
 * @property string|null $requestBody
 * @property CarbonImmutable|null $createdAt
 */
class WebhookLog extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'         => 'int',
        'webhookId'  => 'int',
        'statusCode' => 'int',
        'event'      => WebhookEvent::class,
        'createdAt'  => CarbonImmutable::class,
    ];

    /**
     * Check if the webhook delivery was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}

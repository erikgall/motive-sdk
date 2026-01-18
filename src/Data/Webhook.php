<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\WebhookEvent;
use Motive\Enums\WebhookStatus;

/**
 * Webhook data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $url
 * @property array<int, WebhookEvent> $events
 * @property WebhookStatus $status
 * @property string|null $secret
 * @property string|null $description
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Webhook extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'companyId' => 'int',
        'status'    => WebhookStatus::class,
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];

    /**
     * Process attributes after normalization.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function processAttributes(array $attributes): array
    {
        if (isset($attributes['events']) && is_array($attributes['events'])) {
            $attributes['events'] = array_map(
                fn (string|WebhookEvent $event) => $event instanceof WebhookEvent ? $event : WebhookEvent::from($event),
                $attributes['events']
            );
        }

        return $attributes;
    }
}

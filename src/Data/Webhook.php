<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\WebhookEvent;
use Motive\Enums\WebhookStatus;

/**
 * Webhook data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Webhook extends DataTransferObject
{
    /**
     * @param  array<int, WebhookEvent>  $events
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public string $url,
        public array $events,
        public WebhookStatus $status,
        public ?string $secret = null,
        public ?string $description = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['createdAt', 'updatedAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'status' => WebhookStatus::class,
        ];
    }

    /**
     * Custom processing for events array.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function preprocessData(array $data): array
    {
        if (isset($data['events']) && is_array($data['events'])) {
            $data['events'] = array_map(
                fn (string $event) => WebhookEvent::from($event),
                $data['events']
            );
        }

        return $data;
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id' => 'companyId',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];
    }
}

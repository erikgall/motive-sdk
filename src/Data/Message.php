<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\MessageDirection;

/**
 * Message data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Message extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public string $body,
        public MessageDirection $direction,
        public ?int $driverId = null,
        public ?bool $read = null,
        public ?CarbonImmutable $sentAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['sentAt', 'createdAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'direction' => MessageDirection::class,
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
            'company_id' => 'companyId',
            'driver_id'  => 'driverId',
            'sent_at'    => 'sentAt',
            'created_at' => 'createdAt',
        ];
    }
}

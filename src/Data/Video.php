<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Video data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Video extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $requestId,
        public string $url,
        public ?int $duration = null,
        public ?int $fileSize = null,
        public ?string $thumbnailUrl = null,
        public ?CarbonImmutable $expiresAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['expiresAt', 'createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'request_id'    => 'requestId',
            'file_size'     => 'fileSize',
            'thumbnail_url' => 'thumbnailUrl',
            'expires_at'    => 'expiresAt',
            'created_at'    => 'createdAt',
        ];
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Video data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $requestId
 * @property string $url
 * @property int|null $duration
 * @property int|null $fileSize
 * @property string|null $thumbnailUrl
 * @property CarbonImmutable|null $expiresAt
 * @property CarbonImmutable|null $createdAt
 */
class Video extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'requestId' => 'int',
        'duration'  => 'int',
        'fileSize'  => 'int',
        'expiresAt' => CarbonImmutable::class,
        'createdAt' => CarbonImmutable::class,
    ];
}

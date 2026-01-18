<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\MessageDirection;

/**
 * Message data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $body
 * @property MessageDirection $direction
 * @property int|null $driverId
 * @property bool|null $read
 * @property CarbonImmutable|null $sentAt
 * @property CarbonImmutable|null $createdAt
 */
class Message extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'companyId' => 'int',
        'driverId'  => 'int',
        'read'      => 'bool',
        'direction' => MessageDirection::class,
        'sentAt'    => CarbonImmutable::class,
        'createdAt' => CarbonImmutable::class,
    ];
}

<?php

namespace Motive\Data;

/**
 * Document image data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $url
 * @property string|null $filename
 * @property int|null $size
 * @property int|null $sequence
 */
class DocumentImage extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'       => 'int',
        'size'     => 'int',
        'sequence' => 'int',
    ];
}

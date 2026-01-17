<?php

namespace Motive\Data;

/**
 * Document image data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentImage extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $url,
        public ?string $filename = null,
        public ?int $size = null,
        public ?int $sequence = null
    ) {}
}

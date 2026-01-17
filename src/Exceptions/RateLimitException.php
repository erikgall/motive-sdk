<?php

namespace Motive\Exceptions;

/**
 * Exception thrown when rate limit is exceeded (429).
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class RateLimitException extends MotiveException
{
    /**
     * Get the number of seconds to wait before retrying.
     */
    public function retryAfter(): ?int
    {
        $retryAfter = $this->response?->header('Retry-After');

        return $retryAfter !== null ? (int) $retryAfter : null;
    }
}

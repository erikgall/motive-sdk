<?php

namespace Motive\Contracts;

use Motive\Client\PendingRequest;

/**
 * Contract for authentication strategies.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
interface Authenticator
{
    /**
     * Add authentication to the pending request.
     */
    public function authenticate(PendingRequest $request): PendingRequest;

    /**
     * Check if authentication credentials are expired.
     */
    public function isExpired(): bool;

    /**
     * Refresh authentication credentials.
     */
    public function refresh(): void;
}

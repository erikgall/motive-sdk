<?php

namespace Motive\Auth;

use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;

/**
 * API Key authenticator for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ApiKeyAuthenticator implements Authenticator
{
    public function __construct(
        private readonly string $apiKey
    ) {}

    /**
     * Add the API key header to the request.
     */
    public function authenticate(PendingRequest $request): PendingRequest
    {
        return $request->withHeader('X-Api-Key', $this->apiKey);
    }

    /**
     * API keys never expire.
     */
    public function isExpired(): bool
    {
        return false;
    }

    /**
     * API keys don't need to be refreshed.
     */
    public function refresh(): void
    {
        // API keys don't expire or refresh
    }
}

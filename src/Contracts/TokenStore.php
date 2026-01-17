<?php

namespace Motive\Contracts;

use Carbon\CarbonInterface;

/**
 * Contract for OAuth token storage.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
interface TokenStore
{
    /**
     * Clear all stored tokens.
     */
    public function clear(): void;

    /**
     * Get the stored access token.
     */
    public function getAccessToken(): ?string;

    /**
     * Get the token expiration time.
     */
    public function getExpiresAt(): ?CarbonInterface;

    /**
     * Get the stored refresh token.
     */
    public function getRefreshToken(): ?string;

    /**
     * Store the OAuth tokens.
     */
    public function store(string $accessToken, string $refreshToken, CarbonInterface $expiresAt): void;
}

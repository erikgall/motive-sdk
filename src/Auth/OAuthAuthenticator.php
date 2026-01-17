<?php

namespace Motive\Auth;

use Carbon\CarbonImmutable;
use Motive\Contracts\TokenStore;
use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;

/**
 * OAuth 2.0 authenticator with automatic token refresh.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class OAuthAuthenticator implements Authenticator
{
    private const DEFAULT_EXPIRATION_BUFFER = 300; // 5 minutes

    public function __construct(
        private readonly TokenStore $tokenStore,
        private readonly OAuthFlow $oauthFlow,
        private readonly int $expirationBuffer = self::DEFAULT_EXPIRATION_BUFFER
    ) {}

    /**
     * Add the Bearer token header to the request.
     */
    public function authenticate(PendingRequest $request): PendingRequest
    {
        $accessToken = $this->tokenStore->getAccessToken();

        return $request->withHeader('Authorization', "Bearer {$accessToken}");
    }

    /**
     * Check if the access token is expired (including buffer time).
     */
    public function isExpired(): bool
    {
        $expiresAt = $this->tokenStore->getExpiresAt();

        if ($expiresAt === null) {
            return true;
        }

        return CarbonImmutable::now()
            ->addSeconds($this->expirationBuffer)
            ->isAfter($expiresAt);
    }

    /**
     * Refresh the access token using the refresh token.
     */
    public function refresh(): void
    {
        $refreshToken = $this->tokenStore->getRefreshToken();

        if ($refreshToken === null) {
            return;
        }

        $newToken = $this->oauthFlow->refreshToken($refreshToken);

        $this->tokenStore->store(
            $newToken->accessToken,
            $newToken->refreshToken ?? $refreshToken,
            $newToken->expiresAt
        );
    }
}

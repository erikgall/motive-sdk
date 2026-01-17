<?php

namespace Motive\Auth;

use Carbon\CarbonImmutable;

/**
 * OAuth access token value object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class AccessToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly ?string $refreshToken,
        public readonly CarbonImmutable $expiresAt
    ) {}

    /**
     * Check if the token is expired.
     */
    public function isExpired(int $bufferSeconds = 0): bool
    {
        return CarbonImmutable::now()->addSeconds($bufferSeconds)->isAfter($this->expiresAt);
    }

    /**
     * Convert the token to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'accessToken'  => $this->accessToken,
            'refreshToken' => $this->refreshToken,
            'expiresAt'    => $this->expiresAt->toIso8601String(),
        ];
    }

    /**
     * Create an AccessToken from an array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        $expiresAt = isset($data['expires_at'])
            ? CarbonImmutable::parse($data['expires_at'])
            : CarbonImmutable::now()->addSeconds((int) $data['expires_in']);

        return new self(
            accessToken: $data['access_token'],
            refreshToken: $data['refresh_token'] ?? null,
            expiresAt: $expiresAt
        );
    }
}

<?php

namespace Motive\Tests\Unit\Auth;

use Carbon\CarbonImmutable;
use Motive\Auth\AccessToken;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AccessTokenTest extends TestCase
{
    #[Test]
    public function it_checks_expiration_with_buffer(): void
    {
        // Token expires in 4 minutes - should be considered expired with 5 min buffer
        $token = new AccessToken(
            accessToken: 'test-access-token',
            refreshToken: 'test-refresh-token',
            expiresAt: CarbonImmutable::now()->addMinutes(4)
        );

        $this->assertTrue($token->isExpired(bufferSeconds: 300));
        $this->assertFalse($token->isExpired(bufferSeconds: 60));
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $expiresAt = CarbonImmutable::now()->addHour();

        $token = new AccessToken(
            accessToken: 'test-access-token',
            refreshToken: 'test-refresh-token',
            expiresAt: $expiresAt
        );

        $array = $token->toArray();

        $this->assertSame('test-access-token', $array['accessToken']);
        $this->assertSame('test-refresh-token', $array['refreshToken']);
        $this->assertSame($expiresAt->toIso8601String(), $array['expiresAt']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $expiresAt = CarbonImmutable::now()->addHour();

        $token = AccessToken::from([
            'access_token'  => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'expires_at'    => $expiresAt->toIso8601String(),
        ]);

        $this->assertSame('test-access-token', $token->accessToken);
        $this->assertSame('test-refresh-token', $token->refreshToken);
        $this->assertInstanceOf(CarbonImmutable::class, $token->expiresAt);
    }

    #[Test]
    public function it_creates_with_expires_in(): void
    {
        $token = AccessToken::from([
            'access_token'  => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'expires_in'    => 3600,
        ]);

        $this->assertSame('test-access-token', $token->accessToken);
        $this->assertSame('test-refresh-token', $token->refreshToken);
        $this->assertInstanceOf(CarbonImmutable::class, $token->expiresAt);
        // Should be roughly 1 hour from now
        $this->assertTrue($token->expiresAt->isAfter(CarbonImmutable::now()->addMinutes(59)));
    }

    #[Test]
    public function it_detects_expired_token(): void
    {
        $token = new AccessToken(
            accessToken: 'test-access-token',
            refreshToken: 'test-refresh-token',
            expiresAt: CarbonImmutable::now()->subMinute()
        );

        $this->assertTrue($token->isExpired());
    }

    #[Test]
    public function it_detects_valid_token(): void
    {
        $token = new AccessToken(
            accessToken: 'test-access-token',
            refreshToken: 'test-refresh-token',
            expiresAt: CarbonImmutable::now()->addHour()
        );

        $this->assertFalse($token->isExpired());
    }

    #[Test]
    public function it_handles_nullable_refresh_token(): void
    {
        $token = new AccessToken(
            accessToken: 'test-access-token',
            refreshToken: null,
            expiresAt: CarbonImmutable::now()->addHour()
        );

        $this->assertNull($token->refreshToken);
    }
}

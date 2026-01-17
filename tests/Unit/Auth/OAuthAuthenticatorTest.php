<?php

namespace Motive\Tests\Unit\Auth;

use Motive\Auth\OAuthFlow;
use Carbon\CarbonImmutable;
use Motive\Auth\AccessToken;
use PHPUnit\Framework\TestCase;
use Motive\Contracts\TokenStore;
use Motive\Client\PendingRequest;
use Motive\Auth\OAuthAuthenticator;
use Motive\Contracts\Authenticator;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class OAuthAuthenticatorTest extends TestCase
{
    #[Test]
    public function it_adds_bearer_token_to_request(): void
    {
        $tokenStore = $this->createMock(TokenStore::class);
        $tokenStore->method('getAccessToken')->willReturn('test-access-token');
        $tokenStore->method('getExpiresAt')->willReturn(CarbonImmutable::now()->addHour());

        $authenticator = $this->createAuthenticator($tokenStore);

        // Create a mock authenticator for PendingRequest
        $mockAuth = $this->createMock(Authenticator::class);
        $request = new PendingRequest('https://api.gomotive.com', $mockAuth);

        $authenticatedRequest = $authenticator->authenticate($request);

        $this->assertSame(
            'Bearer test-access-token',
            $authenticatedRequest->getHeaders()['Authorization'] ?? null
        );
    }

    #[Test]
    public function it_considers_expiration_buffer(): void
    {
        $tokenStore = $this->createMock(TokenStore::class);
        // Token expires in 2 minutes, buffer is 5 minutes
        $tokenStore->method('getExpiresAt')->willReturn(CarbonImmutable::now()->addMinutes(2));

        $authenticator = new OAuthAuthenticator(
            tokenStore: $tokenStore,
            oauthFlow: $this->createMock(OAuthFlow::class),
            expirationBuffer: 300
        );

        $this->assertTrue($authenticator->isExpired());
    }

    #[Test]
    public function it_detects_expired_token(): void
    {
        $tokenStore = $this->createMock(TokenStore::class);
        $tokenStore->method('getExpiresAt')->willReturn(CarbonImmutable::now()->subMinute());

        $authenticator = $this->createAuthenticator($tokenStore);

        $this->assertTrue($authenticator->isExpired());
    }

    #[Test]
    public function it_detects_missing_token_as_expired(): void
    {
        $tokenStore = $this->createMock(TokenStore::class);
        $tokenStore->method('getExpiresAt')->willReturn(null);

        $authenticator = $this->createAuthenticator($tokenStore);

        $this->assertTrue($authenticator->isExpired());
    }

    #[Test]
    public function it_detects_valid_token(): void
    {
        $tokenStore = $this->createMock(TokenStore::class);
        $tokenStore->method('getExpiresAt')->willReturn(CarbonImmutable::now()->addHour());

        $authenticator = $this->createAuthenticator($tokenStore);

        $this->assertFalse($authenticator->isExpired());
    }

    #[Test]
    public function it_implements_authenticator_contract(): void
    {
        $authenticator = $this->createAuthenticator();

        $this->assertInstanceOf(Authenticator::class, $authenticator);
    }

    #[Test]
    public function it_refreshes_token_using_oauth_flow(): void
    {
        $tokenStore = $this->createMock(TokenStore::class);
        $tokenStore->method('getRefreshToken')->willReturn('old-refresh-token');
        $tokenStore->expects($this->once())
            ->method('store')
            ->with(
                'new-access-token',
                'new-refresh-token',
                $this->isInstanceOf(CarbonImmutable::class)
            );

        $newToken = new AccessToken(
            accessToken: 'new-access-token',
            refreshToken: 'new-refresh-token',
            expiresAt: CarbonImmutable::now()->addHour()
        );

        $oauthFlow = $this->createMock(OAuthFlow::class);
        $oauthFlow->expects($this->once())
            ->method('refreshToken')
            ->with('old-refresh-token')
            ->willReturn($newToken);

        $authenticator = new OAuthAuthenticator($tokenStore, $oauthFlow);

        $authenticator->refresh();
    }

    /**
     * Create an OAuthAuthenticator instance with mocked dependencies.
     */
    private function createAuthenticator(?TokenStore $tokenStore = null): OAuthAuthenticator
    {
        return new OAuthAuthenticator(
            tokenStore: $tokenStore ?? $this->createMock(TokenStore::class),
            oauthFlow: $this->createMock(OAuthFlow::class)
        );
    }
}

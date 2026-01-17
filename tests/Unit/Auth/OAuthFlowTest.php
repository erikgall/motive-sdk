<?php

namespace Motive\Tests\Unit\Auth;

use Motive\Enums\Scope;
use Motive\Auth\OAuthFlow;
use Motive\Auth\AccessToken;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class OAuthFlowTest extends TestCase
{
    private OAuthFlow $oauthFlow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oauthFlow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback'
        );
    }

    #[Test]
    public function it_allows_custom_base_url(): void
    {
        $oauthFlow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback',
            baseUrl: 'https://api.sandbox.gomotive.com'
        );

        $url = $oauthFlow->authorizationUrl([Scope::VehiclesRead]);

        $this->assertStringStartsWith('https://api.sandbox.gomotive.com/oauth/authorize', $url);
    }

    #[Test]
    public function it_generates_authorization_url(): void
    {
        $url = $this->oauthFlow->authorizationUrl([
            Scope::VehiclesRead,
            Scope::UsersRead,
        ]);

        $this->assertStringContainsString('client_id=test-client-id', $url);
        $this->assertStringContainsString('redirect_uri=', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('scope=vehicles.read+users.read', $url);
    }

    #[Test]
    public function it_generates_authorization_url_with_state(): void
    {
        $url = $this->oauthFlow->authorizationUrl(
            scopes: [Scope::VehiclesRead],
            state: 'random-state-123'
        );

        $this->assertStringContainsString('state=random-state-123', $url);
    }

    #[Test]
    public function it_refreshes_token(): void
    {
        $oauthFlow = $this->getMockedOAuthFlow([
            'access_token'  => 'refreshed-access-token',
            'refresh_token' => 'refreshed-refresh-token',
            'expires_in'    => 3600,
            'token_type'    => 'Bearer',
        ]);

        $token = $oauthFlow->refreshToken('old-refresh-token');

        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertSame('refreshed-access-token', $token->accessToken);
    }

    #[Test]
    public function it_returns_access_token_type(): void
    {
        // Mock the HTTP call for token exchange
        $oauthFlow = $this->getMockedOAuthFlow([
            'access_token'  => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in'    => 3600,
            'token_type'    => 'Bearer',
        ]);

        $token = $oauthFlow->exchangeCode('test-code');

        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertSame('new-access-token', $token->accessToken);
        $this->assertSame('new-refresh-token', $token->refreshToken);
    }

    #[Test]
    public function it_uses_correct_authorization_endpoint(): void
    {
        $url = $this->oauthFlow->authorizationUrl([Scope::VehiclesRead]);

        $this->assertStringStartsWith('https://api.gomotive.com/oauth/authorize', $url);
    }

    /**
     * Create a mocked OAuthFlow for testing HTTP calls.
     *
     * @param  array<string, mixed>  $responseData
     */
    private function getMockedOAuthFlow(array $responseData): OAuthFlow
    {
        $oauthFlow = $this->getMockBuilder(OAuthFlow::class)
            ->setConstructorArgs([
                'clientId'     => 'test-client-id',
                'clientSecret' => 'test-client-secret',
                'redirectUri'  => 'https://example.com/callback',
            ])
            ->onlyMethods(['sendTokenRequest'])
            ->getMock();

        $oauthFlow->method('sendTokenRequest')
            ->willReturn($responseData);

        return $oauthFlow;
    }
}

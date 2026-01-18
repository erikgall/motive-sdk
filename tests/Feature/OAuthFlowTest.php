<?php

namespace Motive\Tests\Feature;

use Motive\Enums\Scope;
use Motive\Auth\OAuthFlow;
use Motive\Tests\TestCase;
use Motive\Auth\AccessToken;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

/**
 * Feature tests for OAuthFlow integration.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class OAuthFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_exchanges_code_for_tokens(): void
    {
        Http::fake([
            'api.gomotive.com/oauth/token' => Http::response([
                'access_token'  => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'token_type'    => 'Bearer',
                'expires_in'    => 3600,
            ], 200),
        ]);

        $flow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback'
        );

        $token = $flow->exchangeCode('authorization-code');

        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertEquals('new-access-token', $token->accessToken);
        $this->assertEquals('new-refresh-token', $token->refreshToken);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/oauth/token')
                && $request['grant_type'] === 'authorization_code'
                && $request['code'] === 'authorization-code';
        });
    }

    #[Test]
    public function it_generates_authorization_url_with_scopes(): void
    {
        $flow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback'
        );

        $url = $flow->authorizationUrl([
            Scope::VehiclesRead,
            Scope::DriversRead,
        ]);

        $this->assertStringContainsString('https://api.gomotive.com/oauth/authorize', $url);
        $this->assertStringContainsString('client_id=test-client-id', $url);
        $this->assertStringContainsString('redirect_uri=', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('scope=', $url);
    }

    #[Test]
    public function it_generates_authorization_url_with_state(): void
    {
        $flow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback'
        );

        $url = $flow->authorizationUrl([Scope::VehiclesRead], 'random-state-value');

        $this->assertStringContainsString('state=random-state-value', $url);
    }

    #[Test]
    public function it_refreshes_access_token(): void
    {
        Http::fake([
            'api.gomotive.com/oauth/token' => Http::response([
                'access_token'  => 'refreshed-access-token',
                'refresh_token' => 'new-refresh-token',
                'token_type'    => 'Bearer',
                'expires_in'    => 3600,
            ], 200),
        ]);

        $flow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback'
        );

        $token = $flow->refreshToken('existing-refresh-token');

        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertEquals('refreshed-access-token', $token->accessToken);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/oauth/token')
                && $request['grant_type'] === 'refresh_token'
                && $request['refresh_token'] === 'existing-refresh-token';
        });
    }

    #[Test]
    public function it_uses_custom_base_url(): void
    {
        Http::fake([
            'custom-api.example.com/oauth/token' => Http::response([
                'access_token'  => 'access-token',
                'refresh_token' => 'refresh-token',
                'token_type'    => 'Bearer',
                'expires_in'    => 3600,
            ], 200),
        ]);

        $flow = new OAuthFlow(
            clientId: 'test-client-id',
            clientSecret: 'test-client-secret',
            redirectUri: 'https://example.com/callback',
            baseUrl: 'https://custom-api.example.com'
        );

        $flow->exchangeCode('code');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'custom-api.example.com');
        });
    }
}

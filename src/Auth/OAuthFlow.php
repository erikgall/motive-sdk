<?php

namespace Motive\Auth;

use Motive\Enums\Scope;
use Illuminate\Support\Facades\Http;

/**
 * OAuth 2.0 authorization flow handler.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class OAuthFlow
{
    private const DEFAULT_BASE_URL = 'https://api.gomotive.com';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri,
        private readonly string $baseUrl = self::DEFAULT_BASE_URL
    ) {}

    /**
     * Generate the OAuth authorization URL.
     *
     * @param  array<int, Scope>  $scopes
     */
    public function authorizationUrl(array $scopes, ?string $state = null): string
    {
        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'scope'         => implode(' ', array_map(fn (Scope $scope) => $scope->value, $scopes)),
        ];

        if ($state !== null) {
            $params['state'] = $state;
        }

        return $this->baseUrl.'/oauth/authorize?'.http_build_query($params);
    }

    /**
     * Exchange an authorization code for access tokens.
     */
    public function exchangeCode(string $code): AccessToken
    {
        $response = $this->sendTokenRequest([
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
        ]);

        return AccessToken::from($response);
    }

    /**
     * Refresh an access token using a refresh token.
     */
    public function refreshToken(string $refreshToken): AccessToken
    {
        $response = $this->sendTokenRequest([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return AccessToken::from($response);
    }

    /**
     * Send a token request to the OAuth server.
     *
     * @param  array<string, string>  $params
     * @return array<string, mixed>
     */
    protected function sendTokenRequest(array $params): array
    {
        $response = Http::asForm()
            ->post($this->baseUrl.'/oauth/token', $params);

        return $response->json();
    }
}

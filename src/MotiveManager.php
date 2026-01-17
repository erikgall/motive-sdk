<?php

namespace Motive;

use InvalidArgumentException;
use Motive\Client\MotiveClient;
use Motive\Contracts\TokenStore;
use Motive\Contracts\Authenticator;
use Motive\Auth\ApiKeyAuthenticator;

/**
 * Manager for Motive API connections.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveManager
{
    protected ?Authenticator $authenticator = null;

    protected ?MotiveClient $client = null;

    /**
     * @var array<string, mixed>
     */
    protected array $contextHeaders = [];

    protected ?string $currentConnection = null;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected array $config
    ) {
        $this->currentConnection = $config['default'] ?? 'default';
    }

    /**
     * Get the HTTP client for making API requests.
     */
    public function client(): MotiveClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $connectionConfig = $this->getConnectionConfig();
        $authenticator = $this->getAuthenticator();

        $this->client = new MotiveClient(
            baseUrl: $connectionConfig['base_url'],
            authenticator: $authenticator,
            timeout: $connectionConfig['timeout'] ?? 30,
            retryTimes: $connectionConfig['retry']['times'] ?? 3,
            retrySleep: $connectionConfig['retry']['sleep'] ?? 100
        );

        return $this->client;
    }

    /**
     * Switch to a different connection.
     */
    public function connection(string $name): static
    {
        if (! isset($this->config['connections'][$name])) {
            throw new InvalidArgumentException("Connection [{$name}] not configured.");
        }

        $instance = clone $this;
        $instance->currentConnection = $name;
        $instance->authenticator = null;
        $instance->client = null;

        return $instance;
    }

    /**
     * Get the authenticator for the current connection.
     */
    public function getAuthenticator(): Authenticator
    {
        if ($this->authenticator !== null) {
            return $this->authenticator;
        }

        $connectionConfig = $this->getConnectionConfig();
        $driver = $connectionConfig['auth_driver'] ?? 'api_key';

        $this->authenticator = match ($driver) {
            'api_key' => $this->createApiKeyAuthenticator($connectionConfig),
            'oauth'   => throw new InvalidArgumentException('OAuth authenticator requires explicit configuration via withOAuth()'),
            default   => throw new InvalidArgumentException("Unknown auth driver: {$driver}"),
        };

        return $this->authenticator;
    }

    /**
     * Get the current connection name.
     */
    public function getCurrentConnection(): string
    {
        return $this->currentConnection;
    }

    /**
     * Create a new instance with a custom API key.
     */
    public function withApiKey(string $apiKey): static
    {
        $instance = clone $this;
        $instance->authenticator = new ApiKeyAuthenticator($apiKey);
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with a custom authenticator.
     */
    public function withAuthenticator(Authenticator $authenticator): static
    {
        $instance = clone $this;
        $instance->authenticator = $authenticator;
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with metric units enabled.
     */
    public function withMetricUnits(bool $enabled = true): static
    {
        $instance = clone $this;
        $instance->contextHeaders['X-Metric-Units'] = $enabled ? 'true' : 'false';
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with OAuth tokens.
     */
    public function withOAuth(string $accessToken, ?string $refreshToken = null, ?TokenStore $tokenStore = null): static
    {
        // This will be implemented in Phase 5 when OAuth is added
        throw new InvalidArgumentException('OAuth support is not yet implemented');
    }

    /**
     * Create a new instance with a specific timezone.
     */
    public function withTimezone(string $timezone): static
    {
        $instance = clone $this;
        $instance->contextHeaders['X-Timezone'] = $timezone;
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with a specific user ID for auditing.
     */
    public function withUserId(int|string $userId): static
    {
        $instance = clone $this;
        $instance->contextHeaders['X-User-Id'] = (string) $userId;
        $instance->client = null;

        return $instance;
    }

    /**
     * Create an API key authenticator from the connection config.
     *
     * @param  array<string, mixed>  $config
     */
    protected function createApiKeyAuthenticator(array $config): ApiKeyAuthenticator
    {
        $apiKey = $config['api_key'] ?? null;

        if ($apiKey === null || $apiKey === '') {
            throw new InvalidArgumentException('API key is required for api_key authentication');
        }

        return new ApiKeyAuthenticator($apiKey);
    }

    /**
     * Get the configuration for the current connection.
     *
     * @return array<string, mixed>
     */
    protected function getConnectionConfig(): array
    {
        $connections = $this->config['connections'] ?? [];
        $connection = $connections[$this->currentConnection] ?? null;

        if ($connection === null) {
            throw new InvalidArgumentException("Connection [{$this->currentConnection}] not configured.");
        }

        return $connection;
    }
}

<?php

namespace Motive\Client;

use Motive\Contracts\Authenticator;

/**
 * Fluent request builder for API requests.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class PendingRequest
{
    /**
     * @var array<string, mixed>
     */
    protected array $body = [];

    /**
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * @var array<string, mixed>
     */
    protected array $query = [];

    protected int $timeout = 30;

    public function __construct(
        protected string $baseUrl,
        protected Authenticator $authenticator
    ) {}

    /**
     * Get the authenticator.
     */
    public function getAuthenticator(): Authenticator
    {
        return $this->authenticator;
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the request body.
     *
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get the headers.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the query parameters.
     *
     * @return array<string, mixed>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Get the timeout.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Set the request timeout.
     */
    public function timeout(int $seconds): static
    {
        $clone = clone $this;
        $clone->timeout = $seconds;

        return $clone;
    }

    /**
     * Set the request body.
     *
     * @param  array<string, mixed>  $body
     */
    public function withBody(array $body): static
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * Add a header to the request.
     */
    public function withHeader(string $name, string $value): static
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;

        return $clone;
    }

    /**
     * Add multiple headers to the request.
     *
     * @param  array<string, string>  $headers
     */
    public function withHeaders(array $headers): static
    {
        $clone = clone $this;
        $clone->headers = array_merge($clone->headers, $headers);

        return $clone;
    }

    /**
     * Set query parameters.
     *
     * @param  array<string, mixed>  $query
     */
    public function withQuery(array $query): static
    {
        $clone = clone $this;
        $clone->query = array_merge($clone->query, $query);

        return $clone;
    }
}

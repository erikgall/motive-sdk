<?php

namespace Motive\Client;

use Illuminate\Http\Client\Response as LaravelResponse;

/**
 * Wrapper for HTTP response providing convenient accessors.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Response
{
    public function __construct(
        protected LaravelResponse $response
    ) {}

    /**
     * Get the raw response body.
     */
    public function body(): string
    {
        return $this->response->body();
    }

    /**
     * Check if the response is a client error (4xx).
     */
    public function clientError(): bool
    {
        return $this->response->clientError();
    }

    /**
     * Check if the response indicates a failure.
     */
    public function failed(): bool
    {
        return $this->response->failed();
    }

    /**
     * Get a header value from the response.
     */
    public function header(string $header): ?string
    {
        return $this->response->header($header);
    }

    /**
     * Get the JSON decoded response body.
     *
     * @return mixed
     */
    public function json(?string $key = null): mixed
    {
        return $this->response->json($key);
    }

    /**
     * Check if the response is a server error (5xx).
     */
    public function serverError(): bool
    {
        return $this->response->serverError();
    }

    /**
     * Get the HTTP status code.
     */
    public function status(): int
    {
        return $this->response->status();
    }

    /**
     * Check if the response was successful (2xx).
     */
    public function successful(): bool
    {
        return $this->response->successful();
    }

    /**
     * Get the underlying Laravel response.
     */
    public function toLaravelResponse(): LaravelResponse
    {
        return $this->response;
    }
}

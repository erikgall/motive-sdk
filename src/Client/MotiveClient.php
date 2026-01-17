<?php

namespace Motive\Client;

use Motive\Contracts\Authenticator;
use Illuminate\Support\Facades\Http;
use Motive\Exceptions\ServerException;
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\RateLimitException;
use Motive\Exceptions\ValidationException;
use Motive\Exceptions\AuthorizationException;
use Motive\Exceptions\AuthenticationException;
use Illuminate\Http\Client\PendingRequest as LaravelPendingRequest;

/**
 * HTTP client for making API requests.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveClient
{
    public function __construct(
        protected string $baseUrl,
        protected Authenticator $authenticator,
        protected int $timeout = 30,
        protected int $retryTimes = 3,
        protected int $retrySleep = 100
    ) {}

    /**
     * Create a new pending request builder.
     */
    public function createRequest(): PendingRequest
    {
        return (new PendingRequest($this->baseUrl, $this->authenticator))
            ->timeout($this->timeout);
    }

    /**
     * Send a DELETE request.
     */
    public function delete(string $path): Response
    {
        return $this->send('DELETE', $path);
    }

    /**
     * Send a GET request.
     *
     * @param  array<string, mixed>  $query
     */
    public function get(string $path, array $query = []): Response
    {
        return $this->send('GET', $path, $query);
    }

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
     * Send a PATCH request.
     *
     * @param  array<string, mixed>  $data
     */
    public function patch(string $path, array $data = []): Response
    {
        return $this->send('PATCH', $path, [], $data);
    }

    /**
     * Send a POST request.
     *
     * @param  array<string, mixed>  $data
     */
    public function post(string $path, array $data = []): Response
    {
        return $this->send('POST', $path, [], $data);
    }

    /**
     * Send a PUT request.
     *
     * @param  array<string, mixed>  $data
     */
    public function put(string $path, array $data = []): Response
    {
        return $this->send('PUT', $path, [], $data);
    }

    /**
     * Build the Laravel HTTP client with configuration.
     */
    protected function buildHttpClient(PendingRequest $pendingRequest): LaravelPendingRequest
    {
        $client = Http::timeout($pendingRequest->getTimeout())
            ->withHeaders($pendingRequest->getHeaders())
            ->acceptJson()
            ->asJson();

        if ($this->retryTimes > 0) {
            $client = $client->retry($this->retryTimes, $this->retrySleep, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException
                    || ($exception instanceof \Illuminate\Http\Client\RequestException
                        && $exception->response?->status() >= 500);
            }, throw: false);
        }

        return $client;
    }

    /**
     * Build the full URL for a path.
     */
    protected function buildUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
    }

    /**
     * Handle error responses and throw appropriate exceptions.
     */
    protected function handleErrors(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $message = $response->json('error') ?? $response->json('message') ?? 'An error occurred';

        match (true) {
            $status === 401 => throw new AuthenticationException($message, $response),
            $status === 403 => throw new AuthorizationException($message, $response),
            $status === 404 => throw new NotFoundException($message, $response),
            $status === 422 => throw new ValidationException($message, $response),
            $status === 429 => throw new RateLimitException($message, $response),
            $status >= 500  => throw new ServerException($message, $response),
            default         => null,
        };
    }

    /**
     * Send a request with the pending request configuration.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $data
     */
    protected function send(string $method, string $path, array $query = [], array $data = []): Response
    {
        $pendingRequest = $this->createRequest()
            ->withQuery($query)
            ->withBody($data);

        $pendingRequest = $this->authenticator->authenticate($pendingRequest);

        $httpClient = $this->buildHttpClient($pendingRequest);
        $url = $this->buildUrl($path);

        $laravelResponse = match ($method) {
            'GET'    => $httpClient->get($url, $pendingRequest->getQuery()),
            'POST'   => $httpClient->post($url, $pendingRequest->getBody()),
            'PUT'    => $httpClient->put($url, $pendingRequest->getBody()),
            'PATCH'  => $httpClient->patch($url, $pendingRequest->getBody()),
            'DELETE' => $httpClient->delete($url),
            default  => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        $response = new Response($laravelResponse);

        $this->handleErrors($response);

        return $response;
    }
}

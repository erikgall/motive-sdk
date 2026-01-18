<?php

namespace Motive\Testing;

use Motive\Client\Response;

/**
 * Fake Motive client for testing.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveFake
{
    /**
     * @var array<string, FakeResponse|array<int, FakeResponse>>
     */
    protected array $fakes = [];

    protected RequestHistory $history;

    /**
     * @var array<string, int>
     */
    protected array $sequenceIndices = [];

    public function __construct()
    {
        $this->history = new RequestHistory;
    }

    /**
     * Assert that nothing was sent.
     */
    public function assertNothingSent(): bool
    {
        return $this->history->isEmpty();
    }

    /**
     * Assert that a request was not sent.
     */
    public function assertNotSent(string $path): bool
    {
        return ! $this->history->hasSent($path);
    }

    /**
     * Assert that a request was sent.
     *
     * @param  callable(array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}): bool|null  $callback
     */
    public function assertSent(string $path, ?callable $callback = null): bool
    {
        $requests = $this->history->forPath($path);

        if (empty($requests)) {
            return false;
        }

        if ($callback === null) {
            return true;
        }

        foreach ($requests as $request) {
            if ($callback($request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assert the number of requests sent.
     */
    public function assertSentCount(int $count): bool
    {
        return $this->history->count() === $count;
    }

    /**
     * Clear all fakes.
     */
    public function clearFakes(): void
    {
        $this->fakes = [];
        $this->sequenceIndices = [];
    }

    /**
     * Clear recorded requests.
     */
    public function clearRecorded(): void
    {
        $this->history->clear();
    }

    /**
     * Send a DELETE request.
     */
    public function delete(string $path): Response
    {
        return $this->send('DELETE', $path, [], []);
    }

    /**
     * Register a fake response for a path.
     */
    public function fake(string $path, FakeResponse $response): static
    {
        $this->fakes[$path] = $response;

        return $this;
    }

    /**
     * Register a sequence of fake responses for a path.
     *
     * @param  array<int, FakeResponse>  $responses
     */
    public function fakeSequence(string $path, array $responses): static
    {
        $this->fakes[$path] = $responses;
        $this->sequenceIndices[$path] = 0;

        return $this;
    }

    /**
     * Send a GET request.
     *
     * @param  array<string, mixed>  $query
     */
    public function get(string $path, array $query = []): Response
    {
        return $this->send('GET', $path, $query, []);
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
     * Get all recorded requests.
     *
     * @return array<int, array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}>
     */
    public function recorded(): array
    {
        return $this->history->all();
    }

    /**
     * Get a fake response for a path.
     */
    protected function getFakeResponse(string $path): FakeResponse
    {
        // Try exact match first
        if (isset($this->fakes[$path])) {
            return $this->resolveFake($path, $this->fakes[$path]);
        }

        // Try wildcard matches
        foreach ($this->fakes as $pattern => $fake) {
            if ($this->pathMatches($path, $pattern)) {
                return $this->resolveFake($pattern, $fake);
            }
        }

        // Return empty response if not faked
        return FakeResponse::empty();
    }

    /**
     * Check if a path matches a pattern (supports wildcards).
     */
    protected function pathMatches(string $actualPath, string $pattern): bool
    {
        if ($actualPath === $pattern) {
            return true;
        }

        if (! str_contains($pattern, '*')) {
            return false;
        }

        $regex = str_replace(['*', '/'], ['[^/]+', '\/'], $pattern);

        return (bool) preg_match('/^'.$regex.'$/', $actualPath);
    }

    /**
     * Resolve a fake to a FakeResponse.
     *
     * @param  FakeResponse|array<int, FakeResponse>  $fake
     */
    protected function resolveFake(string $path, FakeResponse|array $fake): FakeResponse
    {
        if ($fake instanceof FakeResponse) {
            return $fake;
        }

        // Handle sequence
        $index = $this->sequenceIndices[$path] ?? 0;
        $response = $fake[$index] ?? $fake[count($fake) - 1];

        $this->sequenceIndices[$path] = $index + 1;

        return $response;
    }

    /**
     * Send a request.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $data
     */
    protected function send(string $method, string $path, array $query, array $data): Response
    {
        $this->history->record($method, $path, $query, $data);

        $fakeResponse = $this->getFakeResponse($path);

        return $fakeResponse->toResponse();
    }
}

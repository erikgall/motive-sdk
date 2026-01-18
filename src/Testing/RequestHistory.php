<?php

namespace Motive\Testing;

/**
 * Records and queries request history for testing.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class RequestHistory
{
    /**
     * @var array<int, array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}>
     */
    protected array $requests = [];

    /**
     * Get all recorded requests.
     *
     * @return array<int, array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}>
     */
    public function all(): array
    {
        return $this->requests;
    }

    /**
     * Clear all recorded requests.
     */
    public function clear(): void
    {
        $this->requests = [];
    }

    /**
     * Get the count of recorded requests.
     */
    public function count(): int
    {
        return count($this->requests);
    }

    /**
     * Filter requests by a callback.
     *
     * @param  callable(array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}): bool  $callback
     * @return array<int, array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}>
     */
    public function filter(callable $callback): array
    {
        return array_values(array_filter($this->requests, $callback));
    }

    /**
     * Get the first recorded request.
     *
     * @return array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}|null
     */
    public function first(): ?array
    {
        return $this->requests[0] ?? null;
    }

    /**
     * Get requests for a specific method.
     *
     * @return array<int, array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}>
     */
    public function forMethod(string $method): array
    {
        return $this->filter(fn (array $request) => $request['method'] === strtoupper($method));
    }

    /**
     * Get requests for a specific path.
     *
     * @return array<int, array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}>
     */
    public function forPath(string $path): array
    {
        return $this->filter(fn (array $request) => $this->pathMatches($request['path'], $path));
    }

    /**
     * Check if a path was requested.
     */
    public function hasSent(string $path): bool
    {
        foreach ($this->requests as $request) {
            if ($this->pathMatches($request['path'], $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the history is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->requests);
    }

    /**
     * Get the last recorded request.
     *
     * @return array{method: string, path: string, query: array<string, mixed>, data: array<string, mixed>, timestamp: float}|null
     */
    public function last(): ?array
    {
        if (empty($this->requests)) {
            return null;
        }

        return $this->requests[count($this->requests) - 1];
    }

    /**
     * Record a request.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $data
     */
    public function record(string $method, string $path, array $query, array $data): void
    {
        $this->requests[] = [
            'method'    => strtoupper($method),
            'path'      => $path,
            'query'     => $query,
            'data'      => $data,
            'timestamp' => microtime(true),
        ];
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
}

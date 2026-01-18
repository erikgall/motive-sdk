<?php

namespace Motive\Testing;

use Motive\Client\Response;

/**
 * Fake response builder for testing.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FakeResponse
{
    /**
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected array $data,
        protected int $status = 200
    ) {}

    /**
     * Get the response data.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the response headers.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the response status.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Convert to a Response instance.
     */
    public function toResponse(): Response
    {
        $httpResponse = new FakeHttpResponse($this->data, $this->status, $this->headers);

        return new Response($httpResponse);
    }

    /**
     * Add headers to the response.
     *
     * @param  array<string, string>  $headers
     */
    public function withHeaders(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Create an empty response.
     */
    public static function empty(int $status = 200): static
    {
        return new static([], $status);
    }

    /**
     * Create an error response.
     *
     * @param  array<string, mixed>  $body
     */
    public static function error(int $status, array $body = []): static
    {
        return new static($body, $status);
    }

    /**
     * Create a forbidden (403) response.
     */
    public static function forbidden(string $message = 'Access denied'): static
    {
        return new static(['error' => $message], 403);
    }

    /**
     * Create a JSON response.
     *
     * @param  array<string, mixed>  $data
     */
    public static function json(array $data, int $status = 200): static
    {
        return new static($data, $status);
    }

    /**
     * Create a not found (404) response.
     */
    public static function notFound(string $message = 'Resource not found'): static
    {
        return new static(['error' => $message], 404);
    }

    /**
     * Create a paginated response.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    public static function paginated(array $items, int $total, int $perPage, string $key, int $currentPage = 1): static
    {
        $lastPage = (int) ceil($total / $perPage);

        return new static([
            $key         => $items,
            'pagination' => [
                'total'          => $total,
                'per_page'       => $perPage,
                'current_page'   => $currentPage,
                'last_page'      => $lastPage,
                'has_more_pages' => $currentPage < $lastPage,
            ],
        ]);
    }

    /**
     * Create a rate limit (429) response.
     */
    public static function rateLimit(int $retryAfter = 60): static
    {
        return new static([
            'error'       => 'Rate limit exceeded',
            'retry_after' => $retryAfter,
        ], 429);
    }

    /**
     * Create a server error (500) response.
     */
    public static function serverError(string $message = 'Internal server error'): static
    {
        return new static(['error' => $message], 500);
    }

    /**
     * Create an unauthorized (401) response.
     */
    public static function unauthorized(string $message = 'Invalid API key'): static
    {
        return new static(['error' => $message], 401);
    }

    /**
     * Create a validation error (422) response.
     *
     * @param  array<string, array<int, string>>  $errors
     */
    public static function validationError(array $errors): static
    {
        return new static([
            'message' => 'Validation failed',
            'errors'  => $errors,
        ], 422);
    }
}

<?php

namespace Motive\Resources;

use Illuminate\Support\Str;
use Motive\Client\MotiveClient;

/**
 * Base class for all API resources.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
abstract class Resource
{
    protected string $apiVersion = '1';

    public function __construct(
        protected MotiveClient $client
    ) {}

    /**
     * Build the full API path for this resource.
     */
    public function fullPath(?string $suffix = null): string
    {
        $path = "/v{$this->apiVersion}/{$this->basePath()}";

        return $suffix !== null ? "{$path}/{$suffix}" : $path;
    }

    /**
     * Get the base path for this resource.
     */
    public function getBasePath(): string
    {
        return $this->basePath();
    }

    /**
     * Get the plural resource key (for list responses).
     */
    public function getPluralResourceKey(): string
    {
        return Str::plural($this->resourceKey());
    }

    /**
     * Get the singular resource key.
     */
    public function getResourceKey(): string
    {
        return $this->resourceKey();
    }

    /**
     * Get the base path for this resource.
     */
    abstract protected function basePath(): string;

    /**
     * Get the DTO class for this resource.
     *
     * @return class-string
     */
    abstract protected function dtoClass(): string;

    /**
     * Get the HTTP client.
     */
    protected function getClient(): MotiveClient
    {
        return $this->client;
    }

    /**
     * Get the singular resource key used in API responses.
     */
    abstract protected function resourceKey(): string;
}

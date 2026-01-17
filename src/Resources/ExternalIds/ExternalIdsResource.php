<?php

namespace Motive\Resources\ExternalIds;

use Motive\Data\ExternalId;
use Motive\Resources\Resource;

/**
 * Resource for managing external ID mappings.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ExternalIdsResource extends Resource
{
    /**
     * Delete an external ID mapping.
     */
    public function delete(string $resourceType, int|string $resourceId): bool
    {
        $response = $this->client->delete($this->fullPath("{$resourceType}/{$resourceId}"));

        return $response->successful();
    }

    /**
     * Get an external ID for a resource.
     */
    public function get(string $resourceType, int|string $resourceId): ?string
    {
        $response = $this->client->get($this->fullPath("{$resourceType}/{$resourceId}"));
        $data = $response->json($this->resourceKey());

        return $data['external_id'] ?? null;
    }

    /**
     * Set an external ID for a resource.
     */
    public function set(string $resourceType, int|string $resourceId, string $externalId): ExternalId
    {
        $response = $this->client->put($this->fullPath("{$resourceType}/{$resourceId}"), [
            'external_id' => $externalId,
        ]);

        return ExternalId::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'external_ids';
    }

    /**
     * @return class-string<ExternalId>
     */
    protected function dtoClass(): string
    {
        return ExternalId::class;
    }

    protected function resourceKey(): string
    {
        return 'external_id';
    }
}

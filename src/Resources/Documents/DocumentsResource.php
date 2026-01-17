<?php

namespace Motive\Resources\Documents;

use Motive\Data\Document;
use Motive\Resources\Resource;
use Motive\Enums\DocumentStatus;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing documents.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentsResource extends Resource
{
    /**
     * Delete a document.
     */
    public function delete(int|string $id): bool
    {
        $response = $this->client->delete($this->fullPath((string) $id));

        return $response->successful();
    }

    /**
     * Download a document's content.
     */
    public function download(int|string $id): string
    {
        $response = $this->client->get($this->fullPath("{$id}/download"));

        return $response->body();
    }

    /**
     * Find a document by ID.
     */
    public function find(int|string $id): Document
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return Document::from($response->json($this->resourceKey()));
    }

    /**
     * Get documents for a specific driver.
     *
     * @return LazyCollection<int, Document>
     */
    public function forDriver(int|string $driverId): LazyCollection
    {
        return $this->list(['driver_id' => $driverId]);
    }

    /**
     * List all documents.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Document>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Document::from($item));
    }

    /**
     * Update a document's status.
     */
    public function updateStatus(int|string $id, DocumentStatus $status): Document
    {
        $response = $this->client->patch($this->fullPath((string) $id), [
            $this->resourceKey() => ['status' => $status->value],
        ]);

        return Document::from($response->json($this->resourceKey()));
    }

    /**
     * Upload a new document.
     *
     * @param  array<string, mixed>  $data
     */
    public function upload(array $data): Document
    {
        $response = $this->client->post($this->fullPath(), [
            $this->resourceKey() => $data,
        ]);

        return Document::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'documents';
    }

    /**
     * @return class-string<Document>
     */
    protected function dtoClass(): string
    {
        return Document::class;
    }

    protected function resourceKey(): string
    {
        return 'document';
    }
}

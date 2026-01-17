<?php

namespace Motive\Resources\FormEntries;

use Motive\Data\FormEntry;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing form entries.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormEntriesResource extends Resource
{
    /**
     * Create a new form entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): FormEntry
    {
        $response = $this->client->post($this->fullPath(), [
            $this->resourceKey() => $data,
        ]);

        return FormEntry::from($response->json($this->resourceKey()));
    }

    /**
     * Find a form entry by ID.
     */
    public function find(int|string $id): FormEntry
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return FormEntry::from($response->json($this->resourceKey()));
    }

    /**
     * Get form entries for a specific driver.
     *
     * @return LazyCollection<int, FormEntry>
     */
    public function forDriver(int|string $driverId): LazyCollection
    {
        return $this->list(['driver_id' => $driverId]);
    }

    /**
     * Get form entries for a specific form.
     *
     * @return LazyCollection<int, FormEntry>
     */
    public function forForm(int|string $formId): LazyCollection
    {
        return $this->list(['form_id' => $formId]);
    }

    /**
     * List all form entries.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, FormEntry>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => FormEntry::from($item));
    }

    protected function basePath(): string
    {
        return 'form_entries';
    }

    /**
     * @return class-string<FormEntry>
     */
    protected function dtoClass(): string
    {
        return FormEntry::class;
    }

    protected function resourceKey(): string
    {
        return 'form_entry';
    }
}

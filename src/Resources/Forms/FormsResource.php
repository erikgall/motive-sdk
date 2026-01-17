<?php

namespace Motive\Resources\Forms;

use Motive\Data\Form;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing company forms.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormsResource extends Resource
{
    /**
     * Get only active forms.
     *
     * @return LazyCollection<int, Form>
     */
    public function active(): LazyCollection
    {
        return $this->list(['active' => true]);
    }

    /**
     * Create a new form.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Form
    {
        $response = $this->client->post($this->fullPath(), [
            $this->resourceKey() => $data,
        ]);

        return Form::from($response->json($this->resourceKey()));
    }

    /**
     * Delete a form.
     */
    public function delete(int|string $id): bool
    {
        $response = $this->client->delete($this->fullPath((string) $id));

        return $response->successful();
    }

    /**
     * Find a form by ID.
     */
    public function find(int|string $id): Form
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return Form::from($response->json($this->resourceKey()));
    }

    /**
     * List all forms.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Form>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Form::from($item));
    }

    /**
     * Update a form.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int|string $id, array $data): Form
    {
        $response = $this->client->patch($this->fullPath((string) $id), [
            $this->resourceKey() => $data,
        ]);

        return Form::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'company_forms';
    }

    /**
     * @return class-string<Form>
     */
    protected function dtoClass(): string
    {
        return Form::class;
    }

    protected function resourceKey(): string
    {
        return 'form';
    }
}

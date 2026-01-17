<?php

namespace Motive\Resources\HoursOfService;

use Motive\Data\HosViolation;
use Motive\Resources\Resource;
use Motive\Resources\Concerns\HasCrudOperations;
use Illuminate\Support\LazyCollection;
use Motive\Pagination\LazyPaginator;

/**
 * Resource for managing Hours of Service violations.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosViolationsResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get HOS violations for a specific driver.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, HosViolation>
     */
    public function forDriver(int|string $driverId, array $params = []): array
    {
        $response = $this->client->get($this->fullPath("driver/{$driverId}"), $params);
        $data     = $response->json($this->getPluralResourceKey()) ?? [];

        return array_map(fn (array $item) => HosViolation::from($item), $data);
    }

    /**
     * List all HOS violations.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, HosViolation>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => HosViolation::from($item));
    }

    protected function basePath(): string
    {
        return 'hos_violations';
    }

    /**
     * @return class-string<HosViolation>
     */
    protected function dtoClass(): string
    {
        return HosViolation::class;
    }

    protected function resourceKey(): string
    {
        return 'hos_violation';
    }
}

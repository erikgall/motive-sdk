<?php

namespace Motive\Resources\IftaReports;

use Motive\Data\IftaReport;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing IFTA reports.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class IftaReportsResource extends Resource
{
    /**
     * Find an IFTA report by ID.
     */
    public function find(int|string $id): IftaReport
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return IftaReport::from($response->json($this->resourceKey()));
    }

    /**
     * Get IFTA reports for a specific year.
     *
     * @return LazyCollection<int, IftaReport>
     */
    public function forYear(int $year): LazyCollection
    {
        return $this->list(['year' => $year]);
    }

    /**
     * Generate a new IFTA report.
     *
     * @param  array<string, mixed>  $params
     */
    public function generate(array $params): IftaReport
    {
        $response = $this->client->post($this->fullPath(), [
            $this->resourceKey() => $params,
        ]);

        return IftaReport::from($response->json($this->resourceKey()));
    }

    /**
     * List all IFTA reports.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, IftaReport>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => IftaReport::from($item));
    }

    protected function basePath(): string
    {
        return 'ifta_reports';
    }

    /**
     * @return class-string<IftaReport>
     */
    protected function dtoClass(): string
    {
        return IftaReport::class;
    }

    protected function resourceKey(): string
    {
        return 'ifta_report';
    }
}

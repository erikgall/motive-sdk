<?php

namespace Motive\Resources\Inspections;

use Motive\Resources\Resource;
use Motive\Data\InspectionReport;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing Driver Vehicle Inspection Reports (DVIRs).
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class InspectionReportsResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get inspection reports for a specific driver.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, InspectionReport>
     */
    public function forDriver(int|string $driverId, array $params = []): array
    {
        $response = $this->client->get($this->fullPath("driver/{$driverId}"), $params);
        $data = $response->json($this->getPluralResourceKey()) ?? [];

        return array_map(fn (array $item) => InspectionReport::from($item), $data);
    }

    /**
     * Get inspection reports for a specific vehicle.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, InspectionReport>
     */
    public function forVehicle(int|string $vehicleId, array $params = []): array
    {
        $response = $this->client->get($this->fullPath("vehicle/{$vehicleId}"), $params);
        $data = $response->json($this->getPluralResourceKey()) ?? [];

        return array_map(fn (array $item) => InspectionReport::from($item), $data);
    }

    /**
     * List all inspection reports.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, InspectionReport>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => InspectionReport::from($item));
    }

    protected function basePath(): string
    {
        return 'inspection_reports';
    }

    /**
     * @return class-string<InspectionReport>
     */
    protected function dtoClass(): string
    {
        return InspectionReport::class;
    }

    protected function resourceKey(): string
    {
        return 'inspection_report';
    }
}

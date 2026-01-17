<?php

namespace Motive\Resources\Utilization;

use Motive\Resources\Resource;
use Motive\Data\UtilizationDay;
use Illuminate\Support\Collection;
use Motive\Data\UtilizationReport;

/**
 * Resource for retrieving utilization reports.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class UtilizationResource extends Resource
{
    /**
     * Get daily utilization data.
     *
     * @param  array<string, mixed>  $params
     * @return Collection<int, UtilizationDay>
     */
    public function daily(array $params = []): Collection
    {
        $response = $this->client->get($this->fullPath('daily'), $params);
        $data = $response->json('daily_utilization') ?? [];

        return collect(array_map(fn (array $item) => UtilizationDay::from($item), $data));
    }

    /**
     * Get utilization report for the entire fleet.
     *
     * @param  array<string, mixed>  $params
     */
    public function forFleet(array $params = []): UtilizationReport
    {
        $response = $this->client->get($this->fullPath('fleet'), $params);

        return UtilizationReport::from($response->json($this->resourceKey()));
    }

    /**
     * Get utilization report for a specific vehicle.
     *
     * @param  array<string, mixed>  $params
     */
    public function forVehicle(int|string $vehicleId, array $params = []): UtilizationReport
    {
        $response = $this->client->get($this->fullPath("vehicles/{$vehicleId}"), $params);

        return UtilizationReport::from($response->json($this->resourceKey()));
    }

    /**
     * Get utilization summary.
     *
     * @param  array<string, mixed>  $params
     */
    public function summary(array $params = []): UtilizationReport
    {
        $response = $this->client->get($this->fullPath('summary'), $params);

        return UtilizationReport::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'utilization';
    }

    /**
     * @return class-string<UtilizationReport>
     */
    protected function dtoClass(): string
    {
        return UtilizationReport::class;
    }

    protected function resourceKey(): string
    {
        return 'utilization_report';
    }
}

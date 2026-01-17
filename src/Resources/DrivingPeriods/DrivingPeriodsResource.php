<?php

namespace Motive\Resources\DrivingPeriods;

use Motive\Data\DrivingPeriod;
use Motive\Resources\Resource;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for managing driving periods.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DrivingPeriodsResource extends Resource
{
    /**
     * Find a driving period by ID.
     */
    public function find(int|string $id): DrivingPeriod
    {
        $response = $this->client->get($this->fullPath((string) $id));

        return DrivingPeriod::from($response->json($this->resourceKey()));
    }

    /**
     * Get driving periods for a date range.
     *
     * @return LazyCollection<int, DrivingPeriod>
     */
    public function forDateRange(string $startDate, string $endDate): LazyCollection
    {
        return $this->list([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    /**
     * Get driving periods for a specific driver.
     *
     * @return LazyCollection<int, DrivingPeriod>
     */
    public function forDriver(int|string $driverId): LazyCollection
    {
        return $this->list(['driver_id' => $driverId]);
    }

    /**
     * Get driving periods for a specific vehicle.
     *
     * @return LazyCollection<int, DrivingPeriod>
     */
    public function forVehicle(int|string $vehicleId): LazyCollection
    {
        return $this->list(['vehicle_id' => $vehicleId]);
    }

    /**
     * List all driving periods.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, DrivingPeriod>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => DrivingPeriod::from($item));
    }

    protected function basePath(): string
    {
        return 'driving_periods';
    }

    /**
     * @return class-string<DrivingPeriod>
     */
    protected function dtoClass(): string
    {
        return DrivingPeriod::class;
    }

    protected function resourceKey(): string
    {
        return 'driving_period';
    }
}

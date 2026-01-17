<?php

namespace Motive\Resources\FuelPurchases;

use Motive\Data\FuelPurchase;
use Motive\Resources\Resource;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing fuel purchases.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FuelPurchasesResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get fuel purchases within a date range.
     *
     * @return LazyCollection<int, FuelPurchase>
     */
    public function forDateRange(string $startDate, string $endDate): LazyCollection
    {
        return $this->list([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    /**
     * Get fuel purchases for a specific vehicle.
     *
     * @return LazyCollection<int, FuelPurchase>
     */
    public function forVehicle(int|string $vehicleId): LazyCollection
    {
        return $this->list(['vehicle_id' => $vehicleId]);
    }

    protected function basePath(): string
    {
        return 'fuel_purchases';
    }

    /**
     * @return class-string<FuelPurchase>
     */
    protected function dtoClass(): string
    {
        return FuelPurchase::class;
    }

    protected function resourceKey(): string
    {
        return 'fuel_purchase';
    }
}

<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Fuel purchase data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FuelPurchase extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $companyId,
        public string $fuelType,
        public float $quantity,
        public float $totalCost,
        public ?int $vehicleId = null,
        public ?int $driverId = null,
        public ?float $unitPrice = null,
        public ?int $odometer = null,
        public ?string $vendorName = null,
        public ?string $vendorAddress = null,
        public ?string $receiptNumber = null,
        public ?CarbonImmutable $purchasedAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['purchasedAt', 'createdAt'];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'     => 'companyId',
            'vehicle_id'     => 'vehicleId',
            'driver_id'      => 'driverId',
            'fuel_type'      => 'fuelType',
            'unit_price'     => 'unitPrice',
            'total_cost'     => 'totalCost',
            'vendor_name'    => 'vendorName',
            'vendor_address' => 'vendorAddress',
            'receipt_number' => 'receiptNumber',
            'purchased_at'   => 'purchasedAt',
            'created_at'     => 'createdAt',
        ];
    }
}

<?php

namespace Motive\Data;

/**
 * IFTA jurisdiction data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class IftaJurisdiction extends DataTransferObject
{
    public function __construct(
        public string $jurisdictionCode,
        public string $jurisdictionName,
        public float $totalMiles,
        public ?float $taxableMiles = null,
        public ?float $fuelGallons = null,
        public ?float $taxPaidGallons = null,
        public ?float $netTaxable = null,
        public ?float $taxRate = null,
        public ?float $taxDue = null
    ) {}

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'jurisdiction_code' => 'jurisdictionCode',
            'jurisdiction_name' => 'jurisdictionName',
            'total_miles'       => 'totalMiles',
            'taxable_miles'     => 'taxableMiles',
            'fuel_gallons'      => 'fuelGallons',
            'tax_paid_gallons'  => 'taxPaidGallons',
            'net_taxable'       => 'netTaxable',
            'tax_rate'          => 'taxRate',
            'tax_due'           => 'taxDue',
        ];
    }
}

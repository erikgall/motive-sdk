<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * IFTA report data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class IftaReport extends DataTransferObject
{
    /**
     * @param  array<int, IftaJurisdiction>  $jurisdictions
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public int $quarter,
        public int $year,
        public ?float $totalMiles = null,
        public ?float $totalGallons = null,
        public ?float $mpg = null,
        public ?float $totalTaxDue = null,
        public ?string $status = null,
        public array $jurisdictions = [],
        public ?CarbonImmutable $generatedAt = null,
        public ?CarbonImmutable $createdAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['generatedAt', 'createdAt'];
    }

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [
            'jurisdictions' => IftaJurisdiction::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'    => 'companyId',
            'total_miles'   => 'totalMiles',
            'total_gallons' => 'totalGallons',
            'total_tax_due' => 'totalTaxDue',
            'generated_at'  => 'generatedAt',
            'created_at'    => 'createdAt',
        ];
    }
}

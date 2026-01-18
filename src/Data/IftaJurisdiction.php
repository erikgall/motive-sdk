<?php

namespace Motive\Data;

/**
 * IFTA jurisdiction data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property string $jurisdictionCode
 * @property string $jurisdictionName
 * @property float $totalMiles
 * @property float|null $taxableMiles
 * @property float|null $fuelGallons
 * @property float|null $taxPaidGallons
 * @property float|null $netTaxable
 * @property float|null $taxRate
 * @property float|null $taxDue
 */
class IftaJurisdiction extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'totalMiles'     => 'float',
        'taxableMiles'   => 'float',
        'fuelGallons'    => 'float',
        'taxPaidGallons' => 'float',
        'netTaxable'     => 'float',
        'taxRate'        => 'float',
        'taxDue'         => 'float',
    ];
}

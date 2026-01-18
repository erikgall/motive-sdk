<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * IFTA report data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property int $quarter
 * @property int $year
 * @property float|null $totalMiles
 * @property float|null $totalGallons
 * @property float|null $mpg
 * @property float|null $totalTaxDue
 * @property string|null $status
 * @property array<int, IftaJurisdiction> $jurisdictions
 * @property CarbonImmutable|null $generatedAt
 * @property CarbonImmutable|null $createdAt
 */
class IftaReport extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'           => 'int',
        'companyId'    => 'int',
        'quarter'      => 'int',
        'year'         => 'int',
        'totalMiles'   => 'float',
        'totalGallons' => 'float',
        'mpg'          => 'float',
        'totalTaxDue'  => 'float',
        'generatedAt'  => CarbonImmutable::class,
        'createdAt'    => CarbonImmutable::class,
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'jurisdictions' => IftaJurisdiction::class,
    ];
}

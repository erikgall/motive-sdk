<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Driver Vehicle Inspection Report (DVIR) defect data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $inspectionId
 * @property string $category
 * @property string $description
 * @property string|null $severity
 * @property bool|null $corrected
 * @property CarbonImmutable|null $correctedAt
 * @property int|null $correctedById
 * @property string|null $notes
 * @property CarbonImmutable|null $createdAt
 */
class InspectionDefect extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'            => 'int',
        'inspectionId'  => 'int',
        'correctedById' => 'int',
        'corrected'     => 'bool',
        'correctedAt'   => CarbonImmutable::class,
        'createdAt'     => CarbonImmutable::class,
    ];
}

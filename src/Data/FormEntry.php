<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Form entry data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $formId
 * @property int $driverId
 * @property int|null $vehicleId
 * @property CarbonImmutable|null $submittedAt
 * @property array<int, array<string, mixed>> $fieldValues
 * @property array<string, mixed>|null $location
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class FormEntry extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'          => 'int',
        'formId'      => 'int',
        'driverId'    => 'int',
        'vehicleId'   => 'int',
        'submittedAt' => CarbonImmutable::class,
        'createdAt'   => CarbonImmutable::class,
        'updatedAt'   => CarbonImmutable::class,
    ];
}

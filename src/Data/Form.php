<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Form data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property string $name
 * @property string|null $description
 * @property bool $active
 * @property array<int, FormField> $fields
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Form extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'companyId' => 'int',
        'active'    => 'bool',
        'createdAt' => CarbonImmutable::class,
        'updatedAt' => CarbonImmutable::class,
    ];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'active' => true,
        'fields' => [],
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'fields' => FormField::class,
    ];
}

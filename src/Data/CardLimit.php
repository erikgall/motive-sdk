<?php

namespace Motive\Data;

/**
 * Motive card limit data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $cardId
 * @property float|null $dailyLimit
 * @property float|null $weeklyLimit
 * @property float|null $monthlyLimit
 * @property float|null $perTransaction
 * @property bool $fuelOnly
 */
class CardLimit extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'             => 'int',
        'cardId'         => 'int',
        'dailyLimit'     => 'float',
        'weeklyLimit'    => 'float',
        'monthlyLimit'   => 'float',
        'perTransaction' => 'float',
        'fuelOnly'       => 'bool',
    ];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'fuelOnly' => false,
    ];
}

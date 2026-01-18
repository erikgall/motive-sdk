<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;

/**
 * Reefer activity data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $vehicleId
 * @property float|null $temperature
 * @property float|null $setpoint
 * @property string|null $mode
 * @property float|null $fuelLevel
 * @property bool $engineRunning
 * @property CarbonImmutable|null $recordedAt
 */
class ReeferActivity extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'            => 'int',
        'vehicleId'     => 'int',
        'temperature'   => 'float',
        'setpoint'      => 'float',
        'fuelLevel'     => 'float',
        'engineRunning' => 'bool',
        'recordedAt'    => CarbonImmutable::class,
    ];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'engineRunning' => false,
    ];
}

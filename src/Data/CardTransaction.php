<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\CardTransactionType;

/**
 * Motive card transaction data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $cardId
 * @property float $amount
 * @property CardTransactionType $transactionType
 * @property string|null $merchantName
 * @property int|null $driverId
 * @property int|null $vehicleId
 * @property array<string, mixed>|null $location
 * @property CarbonImmutable|null $transactionDate
 */
class CardTransaction extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'              => 'int',
        'cardId'          => 'int',
        'amount'          => 'float',
        'driverId'        => 'int',
        'vehicleId'       => 'int',
        'transactionType' => CardTransactionType::class,
        'transactionDate' => CarbonImmutable::class,
    ];
}

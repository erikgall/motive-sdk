<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\CardTransactionType;

/**
 * Motive card transaction data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class CardTransaction extends DataTransferObject
{
    /**
     * @param  array<string, mixed>|null  $location
     */
    public function __construct(
        public int $id,
        public int $cardId,
        public float $amount,
        public CardTransactionType $transactionType,
        public ?string $merchantName = null,
        public ?int $driverId = null,
        public ?int $vehicleId = null,
        public ?array $location = null,
        public ?CarbonImmutable $transactionDate = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['transactionDate'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'transactionType' => CardTransactionType::class,
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
            'card_id'          => 'cardId',
            'transaction_type' => 'transactionType',
            'merchant_name'    => 'merchantName',
            'driver_id'        => 'driverId',
            'vehicle_id'       => 'vehicleId',
            'transaction_date' => 'transactionDate',
        ];
    }
}

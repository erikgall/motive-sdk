<?php

namespace Motive\Data;

/**
 * Motive card limit data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class CardLimit extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $cardId,
        public ?float $dailyLimit = null,
        public ?float $weeklyLimit = null,
        public ?float $monthlyLimit = null,
        public ?float $perTransaction = null,
        public bool $fuelOnly = false
    ) {}

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'card_id'         => 'cardId',
            'daily_limit'     => 'dailyLimit',
            'weekly_limit'    => 'weeklyLimit',
            'monthly_limit'   => 'monthlyLimit',
            'per_transaction' => 'perTransaction',
            'fuel_only'       => 'fuelOnly',
        ];
    }
}

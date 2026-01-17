<?php

namespace Motive\Enums;

/**
 * Motive card transaction type values.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum CardTransactionType: string
{
    case Fuel = 'fuel';
    case Maintenance = 'maintenance';
    case Other = 'other';
    case Parking = 'parking';
    case Toll = 'toll';
}

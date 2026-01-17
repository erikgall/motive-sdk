<?php

namespace Motive\Enums;

/**
 * Document type values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum DocumentType: string
{
    case BillOfLading = 'bill_of_lading';
    case DeliveryReceipt = 'delivery_receipt';
    case FuelReceipt = 'fuel_receipt';
    case Other = 'other';
    case ProofOfDelivery = 'proof_of_delivery';
    case RateConfirmation = 'rate_confirmation';
    case ScaleTicket = 'scale_ticket';
}

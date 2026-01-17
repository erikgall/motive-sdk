<?php

namespace Motive\Enums;

/**
 * Shipment status values for freight visibility.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum ShipmentStatus: string
{
    case Cancelled = 'cancelled';
    case Delivered = 'delivered';
    case InTransit = 'in_transit';
    case Pending = 'pending';
    case PickedUp = 'picked_up';
}

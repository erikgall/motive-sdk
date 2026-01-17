<?php

namespace Motive\Enums;

/**
 * Dispatch stop type options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum StopType: string
{
    case Delivery = 'delivery';
    case Pickup = 'pickup';
    case Waypoint = 'waypoint';
}

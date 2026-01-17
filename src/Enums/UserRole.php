<?php

namespace Motive\Enums;

/**
 * User role options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Dispatcher = 'dispatcher';
    case Driver = 'driver';
    case FleetManager = 'fleet_manager';
    case Mechanic = 'mechanic';
}

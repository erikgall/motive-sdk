<?php

namespace Motive\Enums;

/**
 * Vehicle status options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum VehicleStatus: string
{
    case Active = 'active';
    case Decommissioned = 'decommissioned';
    case Inactive = 'inactive';
}

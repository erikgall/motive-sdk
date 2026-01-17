<?php

namespace Motive\Enums;

/**
 * Driver duty status options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum DutyStatus: string
{
    case Driving = 'driving';
    case OffDuty = 'off_duty';
    case OnDuty = 'on_duty';
    case PersonalConveyance = 'personal_conveyance';
    case SleeperBerth = 'sleeper_berth';
    case YardMove = 'yard_move';
}

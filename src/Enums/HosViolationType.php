<?php

namespace Motive\Enums;

/**
 * Hours of Service violation type options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum HosViolationType: string
{
    case Break = 'break';
    case CycleTime = 'cycle_time';
    case DriveTime = 'drive_time';
    case ShiftTime = 'shift_time';
}

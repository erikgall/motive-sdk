<?php

namespace Motive\Enums;

/**
 * Timecard status values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum TimecardStatus: string
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';
    case Submitted = 'submitted';
}

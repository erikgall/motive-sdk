<?php

namespace Motive\Enums;

/**
 * Dispatch status options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum DispatchStatus: string
{
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case InProgress = 'in_progress';
    case Pending = 'pending';
}

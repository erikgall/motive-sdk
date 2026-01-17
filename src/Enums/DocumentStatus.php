<?php

namespace Motive\Enums;

/**
 * Document status values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum DocumentStatus: string
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';
}

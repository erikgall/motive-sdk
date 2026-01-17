<?php

namespace Motive\Enums;

/**
 * User status options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
}

<?php

namespace Motive\Enums;

/**
 * Asset status options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum AssetStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

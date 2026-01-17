<?php

namespace Motive\Enums;

/**
 * Asset type options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum AssetType: string
{
    case Chassis = 'chassis';
    case Container = 'container';
    case Equipment = 'equipment';
    case Trailer = 'trailer';
}

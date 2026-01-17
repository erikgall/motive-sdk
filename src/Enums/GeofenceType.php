<?php

namespace Motive\Enums;

/**
 * Geofence type options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum GeofenceType: string
{
    case Circle = 'circle';
    case Polygon = 'polygon';
}

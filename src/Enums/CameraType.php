<?php

namespace Motive\Enums;

/**
 * Camera type values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum CameraType: string
{
    case Dashcam = 'dashcam';
    case DriverFacing = 'driver_facing';
    case DualFacing = 'dual_facing';
    case RoadFacing = 'road_facing';
    case SideFacing = 'side_facing';
}

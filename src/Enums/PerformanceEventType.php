<?php

namespace Motive\Enums;

/**
 * Driver performance event types for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum PerformanceEventType: string
{
    case CollisionWarning = 'collision_warning';
    case DistractedDriving = 'distracted_driving';
    case DrowsyDriving = 'drowsy_driving';
    case FollowingDistance = 'following_distance';
    case HardBraking = 'hard_braking';
    case HarshCornering = 'harsh_cornering';
    case LaneDeparture = 'lane_departure';
    case PhoneUsage = 'phone_usage';
    case RapidAcceleration = 'rapid_acceleration';
    case Seatbelt = 'seatbelt';
    case Speeding = 'speeding';
    case StopSignViolation = 'stop_sign_violation';
}

<?php

namespace Motive\Enums;

/**
 * Webhook event types for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum WebhookEvent: string
{
    case AssetCreated = 'asset.created';
    case AssetDeleted = 'asset.deleted';
    case AssetUpdated = 'asset.updated';
    case DispatchCreated = 'dispatch.created';
    case DispatchDeleted = 'dispatch.deleted';
    case DispatchUpdated = 'dispatch.updated';
    case DocumentCreated = 'document.created';
    case DocumentUpdated = 'document.updated';
    case DriverCreated = 'driver.created';
    case DriverHosStatusChanged = 'driver.hos_status_changed';
    case DriverHosViolation = 'driver.hos_violation';
    case DriverUpdated = 'driver.updated';
    case FaultCodeDetected = 'fault_code.detected';
    case FaultCodeResolved = 'fault_code.resolved';
    case GeofenceEntered = 'geofence.entered';
    case GeofenceExited = 'geofence.exited';
    case InspectionSubmitted = 'inspection.submitted';
    case MessageReceived = 'message.received';
    case MessageSent = 'message.sent';
    case UserCreated = 'user.created';
    case UserUpdated = 'user.updated';
    case VehicleCreated = 'vehicle.created';
    case VehicleLocationUpdated = 'vehicle.location_updated';
    case VehicleUpdated = 'vehicle.updated';
}

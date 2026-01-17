<?php

namespace Motive\Enums;

/**
 * OAuth scopes for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum Scope: string
{
    case AssetsRead = 'assets.read';
    case AssetsWrite = 'assets.write';
    case CompaniesRead = 'companies.read';
    case DispatchesRead = 'dispatches.read';
    case DispatchesWrite = 'dispatches.write';
    case DocumentsRead = 'documents.read';
    case DocumentsWrite = 'documents.write';
    case DriversRead = 'drivers.read';
    case DriversWrite = 'drivers.write';
    case FuelRead = 'fuel.read';
    case GeofencesRead = 'geofences.read';
    case GeofencesWrite = 'geofences.write';
    case GroupsRead = 'groups.read';
    case GroupsWrite = 'groups.write';
    case HosRead = 'hos.read';
    case HosWrite = 'hos.write';
    case InspectionsRead = 'inspections.read';
    case LocationsRead = 'locations.read';
    case LocationsWrite = 'locations.write';
    case MessagesRead = 'messages.read';
    case MessagesWrite = 'messages.write';
    case UsersRead = 'users.read';
    case UsersWrite = 'users.write';
    case VehiclesRead = 'vehicles.read';
    case VehiclesWrite = 'vehicles.write';
    case WebhooksRead = 'webhooks.read';
    case WebhooksWrite = 'webhooks.write';
}

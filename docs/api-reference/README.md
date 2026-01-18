# API Reference

This section provides detailed documentation for all 31 API resources available in the Motive SDK.

## Resource Categories

### Fleet

Resources for managing your vehicle fleet:

| Resource | Description |
|----------|-------------|
| [Vehicles](fleet/vehicles.md) | Fleet vehicle management |
| [Assets](fleet/assets.md) | Trailers and equipment |
| [Vehicle Gateways](fleet/vehicle-gateways.md) | ELD device information |
| [Fault Codes](fleet/fault-codes.md) | Vehicle diagnostic codes |

### Drivers

Resources for driver management:

| Resource | Description |
|----------|-------------|
| [Users](drivers/users.md) | Driver and staff management |
| [Driving Periods](drivers/driving-periods.md) | Driver activity tracking |
| [Scorecard](drivers/scorecard.md) | Driver safety scores |

### Compliance

Resources for regulatory compliance:

| Resource | Description |
|----------|-------------|
| [HOS Logs](compliance/hos-logs.md) | Hours of Service records |
| [HOS Availability](compliance/hos-availability.md) | Remaining drive time |
| [HOS Violations](compliance/hos-violations.md) | HOS rule violations |
| [Inspection Reports](compliance/inspection-reports.md) | DVIR reports |
| [IFTA Reports](compliance/ifta-reports.md) | Fuel tax reports |

### Dispatch

Resources for dispatch and routing:

| Resource | Description |
|----------|-------------|
| [Dispatches](dispatch/dispatches.md) | Load and route management |
| [Locations](dispatch/locations.md) | Named locations |
| [Geofences](dispatch/geofences.md) | Geographic boundaries |

### Communication

Resources for driver communication:

| Resource | Description |
|----------|-------------|
| [Messages](communication/messages.md) | Driver messaging |
| [Documents](communication/documents.md) | Document management |

### Safety

Resources for safety monitoring:

| Resource | Description |
|----------|-------------|
| [Driver Performance](safety/driver-performance.md) | Safety events |
| [Camera Connections](safety/camera-connections.md) | Dashboard cameras |
| [Camera Control](safety/camera-control.md) | Video retrieval |

### Operations

Resources for fleet operations:

| Resource | Description |
|----------|-------------|
| [Groups](operations/groups.md) | Organizational groups |
| [Companies](operations/companies.md) | Company information |
| [Forms](operations/forms.md) | Custom form templates |
| [Form Entries](operations/form-entries.md) | Submitted form data |
| [Timecards](operations/timecards.md) | Time tracking |
| [Utilization](operations/utilization.md) | Vehicle utilization |

### Financial

Resources for financial tracking:

| Resource | Description |
|----------|-------------|
| [Fuel Purchases](financial/fuel-purchases.md) | Fuel transactions |
| [Motive Card](financial/motive-card.md) | Fuel card management |

### Integration

Resources for system integration:

| Resource | Description |
|----------|-------------|
| [Webhooks](integration/webhooks.md) | Event subscriptions |
| [External IDs](integration/external-ids.md) | External system mappings |
| [Freight Visibility](integration/freight-visibility.md) | Shipment tracking |
| [Reefer Activity](integration/reefer-activity.md) | Refrigerated trailer data |

## Quick Reference

### Common Methods

Most resources support these standard CRUD operations:

| Method | Description | Example |
|--------|-------------|---------|
| `list()` | List all resources (lazy pagination) | `Motive::vehicles()->list()` |
| `paginate()` | Get a single page | `Motive::vehicles()->paginate(page: 1)` |
| `find($id)` | Get a single resource | `Motive::vehicles()->find(123)` |
| `create($data)` | Create a new resource | `Motive::vehicles()->create([...])` |
| `update($id, $data)` | Update a resource | `Motive::vehicles()->update(123, [...])` |
| `delete($id)` | Delete a resource | `Motive::vehicles()->delete(123)` |

### Accessing Resources

```php
use Motive\Facades\Motive;

// Get a resource instance
$vehiclesResource = Motive::vehicles();

// Chain method calls
$vehicle = Motive::vehicles()->find(123);
```

### With Filters

```php
$vehicles = Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);
```

### With Context Modifiers

```php
$vehicles = Motive::withTimezone('America/Chicago')
    ->withMetricUnits()
    ->vehicles()
    ->list();
```

# Freight Visibility

The Freight Visibility resource provides shipment tracking and ETA information.

## Access

```php
use Motive\Facades\Motive;

$freightVisibility = Motive::freightVisibility();
```

## Methods

| Method | Description |
|--------|-------------|
| `shipments($params)` | List shipments |
| `tracking($shipmentId)` | Get shipment tracking |
| `eta($shipmentId)` | Get ETA information |

## List Shipments

```php
$shipments = Motive::freightVisibility()->shipments([
    'status' => 'in_transit',
]);

foreach ($shipments as $shipment) {
    echo "ID: {$shipment->id}\n";
    echo "Status: {$shipment->status->value}\n";
    echo "Origin: {$shipment->origin}\n";
    echo "Destination: {$shipment->destination}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status |
| `dispatch_id` | int | Filter by dispatch |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `per_page` | int | Items per page |

## Get Shipment Tracking

```php
$tracking = Motive::freightVisibility()->tracking($shipmentId);

echo "Current Location:\n";
echo "Lat: {$tracking->latitude}\n";
echo "Lng: {$tracking->longitude}\n";
echo "Updated: {$tracking->updatedAt}\n";

echo "\nTracking History:\n";
foreach ($tracking->history as $point) {
    echo "{$point->timestamp}: ({$point->latitude}, {$point->longitude})\n";
}
```

## Get ETA Information

```php
$eta = Motive::freightVisibility()->eta($shipmentId);

echo "Destination: {$eta->destination}\n";
echo "ETA: {$eta->estimatedArrival}\n";
echo "Distance Remaining: {$eta->distanceRemaining} miles\n";
echo "Time Remaining: {$eta->timeRemaining} minutes\n";
echo "Status: {$eta->status}\n";
```

## Shipment DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Shipment ID |
| `dispatchId` | int\|null | Associated dispatch |
| `status` | ShipmentStatus | Current status |
| `origin` | string\|null | Origin location |
| `destination` | string\|null | Destination |
| `scheduledPickup` | CarbonImmutable\|null | Scheduled pickup |
| `scheduledDelivery` | CarbonImmutable\|null | Scheduled delivery |
| `actualPickup` | CarbonImmutable\|null | Actual pickup |
| `actualDelivery` | CarbonImmutable\|null | Actual delivery |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## ShipmentTracking DTO

| Property | Type | Description |
|----------|------|-------------|
| `shipmentId` | int | Shipment ID |
| `latitude` | float | Current latitude |
| `longitude` | float | Current longitude |
| `speed` | float\|null | Current speed |
| `heading` | int\|null | Heading in degrees |
| `updatedAt` | CarbonImmutable | Last update |
| `history` | array\|null | Position history |

## ShipmentEta DTO

| Property | Type | Description |
|----------|------|-------------|
| `shipmentId` | int | Shipment ID |
| `destination` | string | Destination |
| `estimatedArrival` | CarbonImmutable | ETA |
| `distanceRemaining` | float | Miles remaining |
| `timeRemaining` | int | Minutes remaining |
| `status` | string | ETA status |
| `confidence` | float\|null | ETA confidence |
| `calculatedAt` | CarbonImmutable | Calculation time |

## ShipmentStatus Enum

| Value | Description |
|-------|-------------|
| `pending` | Not yet started |
| `in_transit` | In transit |
| `delivered` | Delivered |
| `cancelled` | Cancelled |

## Use Cases

### Customer Tracking Portal

```php
class TrackingController extends Controller
{
    public function show(string $trackingNumber)
    {
        $shipments = Motive::freightVisibility()->shipments([
            'external_id' => $trackingNumber,
        ]);

        $shipment = $shipments->first();

        if (! $shipment) {
            abort(404, 'Shipment not found');
        }

        $tracking = Motive::freightVisibility()->tracking($shipment->id);
        $eta = Motive::freightVisibility()->eta($shipment->id);

        return view('tracking.show', compact('shipment', 'tracking', 'eta'));
    }
}
```

### Delivery Alerts

```php
$shipments = Motive::freightVisibility()->shipments([
    'status' => 'in_transit',
]);

foreach ($shipments as $shipment) {
    $eta = Motive::freightVisibility()->eta($shipment->id);

    // Alert if arriving within 30 minutes
    if ($eta->timeRemaining <= 30) {
        Notification::send(
            $shipment->customer,
            new DeliveryApproachingNotification($shipment, $eta)
        );
    }
}
```

### Late Delivery Detection

```php
$shipments = Motive::freightVisibility()->shipments([
    'status' => 'in_transit',
]);

$late = [];
foreach ($shipments as $shipment) {
    if ($shipment->scheduledDelivery) {
        $eta = Motive::freightVisibility()->eta($shipment->id);

        if ($eta->estimatedArrival->isAfter($shipment->scheduledDelivery)) {
            $late[] = [
                'shipment' => $shipment,
                'eta' => $eta,
                'delay' => $eta->estimatedArrival->diffInMinutes($shipment->scheduledDelivery),
            ];
        }
    }
}

echo count($late) . " shipments running late\n";
```

## Related

- [Dispatches](../dispatch/dispatches.md)
- [Vehicles](../fleet/vehicles.md)

# Fuel Purchases

The Fuel Purchases resource tracks fuel transactions across your fleet.

## Access

```php
use Motive\Facades\Motive;

$fuelPurchases = Motive::fuelPurchases();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List fuel purchases |
| `paginate($page, $perPage, $params)` | Get paginated purchases |
| `find($id)` | Find purchase by ID |
| `create($data)` | Create a fuel purchase |
| `update($id, $data)` | Update a fuel purchase |
| `delete($id)` | Delete a fuel purchase |

## List Fuel Purchases

```php
$purchases = Motive::fuelPurchases()->list([
    'start_date' => now()->subDays(30)->toDateString(),
    'vehicle_id' => 123,
]);

foreach ($purchases as $purchase) {
    echo "Vehicle: {$purchase->vehicle->number}\n";
    echo "Gallons: {$purchase->gallons}\n";
    echo "Total: \${$purchase->totalAmount}\n";
    echo "Location: {$purchase->location}\n";
    echo "Date: {$purchase->purchasedAt->format('Y-m-d')}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `vehicle_id` | int | Filter by vehicle |
| `driver_id` | int | Filter by driver |
| `per_page` | int | Items per page |

## Find a Purchase

```php
$purchase = Motive::fuelPurchases()->find($purchaseId);

echo "Vehicle: {$purchase->vehicle->number}";
echo "Gallons: {$purchase->gallons}";
echo "Price/Gallon: \${$purchase->pricePerGallon}";
echo "Total: \${$purchase->totalAmount}";
```

## Create a Fuel Purchase

```php
$purchase = Motive::fuelPurchases()->create([
    'vehicle_id' => 123,
    'driver_id' => 456,
    'gallons' => 150.5,
    'price_per_gallon' => 3.459,
    'total_amount' => 520.58,
    'odometer' => 125430,
    'location' => 'Pilot Travel Center, Dallas TX',
    'purchased_at' => now()->toIso8601String(),
]);
```

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `vehicle_id` | int | Yes | Vehicle ID |
| `driver_id` | int | No | Driver ID |
| `gallons` | float | Yes | Gallons purchased |
| `price_per_gallon` | float | No | Price per gallon |
| `total_amount` | float | Yes | Total cost |
| `odometer` | int | No | Odometer reading |
| `location` | string | No | Purchase location |
| `purchased_at` | string | Yes | Purchase time (ISO 8601) |
| `notes` | string | No | Additional notes |

## Update a Purchase

```php
$purchase = Motive::fuelPurchases()->update($purchaseId, [
    'odometer' => 125435,
    'notes' => 'Updated odometer reading',
]);
```

## Delete a Purchase

```php
$deleted = Motive::fuelPurchases()->delete($purchaseId);
```

## FuelPurchase DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Purchase ID |
| `vehicleId` | int | Vehicle ID |
| `vehicle` | Vehicle\|null | Vehicle details |
| `driverId` | int\|null | Driver ID |
| `driver` | User\|null | Driver details |
| `gallons` | float | Gallons purchased |
| `pricePerGallon` | float\|null | Price per gallon |
| `totalAmount` | float | Total cost |
| `odometer` | int\|null | Odometer reading |
| `location` | string\|null | Purchase location |
| `purchasedAt` | CarbonImmutable | Purchase time |
| `notes` | string\|null | Notes |
| `createdAt` | CarbonImmutable\|null | Record created |

## Use Cases

### Monthly Fuel Report

```php
$purchases = Motive::fuelPurchases()->list([
    'start_date' => now()->startOfMonth()->toDateString(),
    'end_date' => now()->endOfMonth()->toDateString(),
]);

$totalGallons = 0;
$totalCost = 0;

foreach ($purchases as $purchase) {
    $totalGallons += $purchase->gallons;
    $totalCost += $purchase->totalAmount;
}

echo "Monthly Fuel Summary:\n";
echo "Total Gallons: " . number_format($totalGallons, 1) . "\n";
echo "Total Cost: \$" . number_format($totalCost, 2) . "\n";
echo "Average Price: \$" . number_format($totalCost / $totalGallons, 3) . "/gal\n";
```

### Vehicle Fuel Efficiency

```php
$purchases = Motive::fuelPurchases()->list([
    'vehicle_id' => 123,
    'start_date' => now()->subDays(30)->toDateString(),
]);

$sorted = collect($purchases)->sortBy('odometer')->values();

if ($sorted->count() >= 2) {
    $first = $sorted->first();
    $last = $sorted->last();

    $miles = $last->odometer - $first->odometer;
    $gallons = $sorted->sum('gallons');
    $mpg = $miles / $gallons;

    echo "MPG: " . round($mpg, 1) . "\n";
}
```

### Cost Per Mile Analysis

```php
$vehicles = Motive::vehicles()->list(['status' => 'active']);

foreach ($vehicles as $vehicle) {
    $purchases = Motive::fuelPurchases()->list([
        'vehicle_id' => $vehicle->id,
        'start_date' => now()->subDays(30)->toDateString(),
    ]);

    $totalCost = collect($purchases)->sum('totalAmount');
    $utilization = Motive::utilization()->forVehicle($vehicle->id, [
        'start_date' => now()->subDays(30)->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    if ($utilization->totalMiles > 0) {
        $costPerMile = $totalCost / $utilization->totalMiles;
        echo "{$vehicle->number}: \$" . round($costPerMile, 2) . "/mile\n";
    }
}
```

## Related

- [IFTA Reports](../compliance/ifta-reports.md)
- [Utilization](../operations/utilization.md)
- [Motive Card](motive-card.md)

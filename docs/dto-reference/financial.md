# Financial DTOs

## FuelPurchase

Represents a fuel transaction.

```php
use Motive\Data\FuelPurchase;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Purchase ID |
| `vehicleId` | `int` | Vehicle ID |
| `vehicle` | `Vehicle\|null` | Vehicle details |
| `driverId` | `int\|null` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `gallons` | `float` | Gallons purchased |
| `pricePerGallon` | `float\|null` | Price per gallon |
| `totalAmount` | `float` | Total cost |
| `odometer` | `int\|null` | Odometer reading |
| `location` | `string\|null` | Location |
| `purchasedAt` | `CarbonImmutable` | Purchase time |
| `notes` | `string\|null` | Notes |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

### Example

```php
$purchases = Motive::fuelPurchases()->list([
    'vehicle_id' => 123,
]);

foreach ($purchases as $purchase) {
    echo "{$purchase->gallons} gal @ \${$purchase->pricePerGallon}\n";
    echo "Total: \${$purchase->totalAmount}\n";
}
```

---

## MotiveCard

Represents a fuel card.

```php
use Motive\Data\MotiveCard;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Card ID |
| `lastFour` | `string` | Last 4 digits |
| `driverId` | `int\|null` | Assigned driver |
| `driver` | `User\|null` | Driver details |
| `vehicleId` | `int\|null` | Assigned vehicle |
| `status` | `string` | Card status |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## CardTransaction

Represents a card transaction.

```php
use Motive\Data\CardTransaction;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Transaction ID |
| `cardId` | `int` | Card ID |
| `driverId` | `int\|null` | Driver ID |
| `vehicleId` | `int\|null` | Vehicle ID |
| `type` | `CardTransactionType` | Transaction type |
| `amount` | `float` | Amount |
| `merchantName` | `string\|null` | Merchant |
| `merchantCategory` | `string\|null` | Category |
| `location` | `string\|null` | Location |
| `gallons` | `float\|null` | Fuel gallons |
| `pricePerGallon` | `float\|null` | Price |
| `odometer` | `int\|null` | Odometer |
| `transactionAt` | `CarbonImmutable` | Transaction time |

---

## CardLimit

Represents card spending limits.

```php
use Motive\Data\CardLimit;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `cardId` | `int` | Card ID |
| `dailyLimit` | `float\|null` | Daily limit |
| `weeklyLimit` | `float\|null` | Weekly limit |
| `monthlyLimit` | `float\|null` | Monthly limit |
| `perTransactionLimit` | `float\|null` | Per transaction |
| `fuelOnly` | `bool` | Fuel only restriction |

## Related

- [Fuel Purchases Resource](../api-reference/financial/fuel-purchases.md)
- [Motive Card Resource](../api-reference/financial/motive-card.md)
- [CardTransactionType Enum](../enums/type-enums.md)

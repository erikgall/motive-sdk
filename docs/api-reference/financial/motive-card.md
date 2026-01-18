# Motive Card

The Motive Card resource manages fuel cards and transactions.

## Access

```php
use Motive\Facades\Motive;

$motiveCard = Motive::motiveCard();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List fuel cards |
| `transactions($params)` | List card transactions |
| `limits($cardId)` | Get card spending limits |

## List Cards

```php
$cards = Motive::motiveCard()->list();

foreach ($cards as $card) {
    echo "Card: {$card->lastFour}\n";
    echo "Driver: {$card->driverId}\n";
    echo "Status: {$card->status}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_id` | int | Filter by driver |
| `status` | string | Filter by status |
| `per_page` | int | Items per page |

## List Transactions

```php
$transactions = Motive::motiveCard()->transactions([
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

foreach ($transactions as $txn) {
    echo "Date: {$txn->transactionAt->format('Y-m-d')}\n";
    echo "Amount: \${$txn->amount}\n";
    echo "Type: {$txn->type->value}\n";
    echo "Merchant: {$txn->merchantName}\n";
    echo "Location: {$txn->location}\n";
}
```

### Transaction Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `card_id` | int | Filter by card |
| `driver_id` | int | Filter by driver |
| `type` | string | Filter by transaction type |
| `per_page` | int | Items per page |

## Get Card Limits

```php
$limits = Motive::motiveCard()->limits($cardId);

echo "Daily Limit: \${$limits->dailyLimit}\n";
echo "Weekly Limit: \${$limits->weeklyLimit}\n";
echo "Per Transaction: \${$limits->perTransactionLimit}\n";
echo "Fuel Only: " . ($limits->fuelOnly ? 'Yes' : 'No') . "\n";
```

## MotiveCard DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Card ID |
| `lastFour` | string | Last 4 digits |
| `driverId` | int\|null | Assigned driver |
| `driver` | User\|null | Driver details |
| `vehicleId` | int\|null | Assigned vehicle |
| `status` | string | Card status |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## CardTransaction DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Transaction ID |
| `cardId` | int | Card ID |
| `driverId` | int\|null | Driver ID |
| `vehicleId` | int\|null | Vehicle ID |
| `type` | CardTransactionType | Transaction type |
| `amount` | float | Amount |
| `merchantName` | string\|null | Merchant name |
| `merchantCategory` | string\|null | Category |
| `location` | string\|null | Location |
| `gallons` | float\|null | Fuel gallons |
| `pricePerGallon` | float\|null | Price per gallon |
| `odometer` | int\|null | Odometer reading |
| `transactionAt` | CarbonImmutable | Transaction time |

## CardLimit DTO

| Property | Type | Description |
|----------|------|-------------|
| `cardId` | int | Card ID |
| `dailyLimit` | float\|null | Daily spending limit |
| `weeklyLimit` | float\|null | Weekly limit |
| `monthlyLimit` | float\|null | Monthly limit |
| `perTransactionLimit` | float\|null | Per transaction limit |
| `fuelOnly` | bool | Fuel purchases only |

## CardTransactionType Enum

| Value | Description |
|-------|-------------|
| `fuel` | Fuel purchase |
| `maintenance` | Vehicle maintenance |
| `other` | Other purchase |

## Use Cases

### Spending Report

```php
$transactions = Motive::motiveCard()->transactions([
    'start_date' => now()->startOfMonth()->toDateString(),
    'end_date' => now()->endOfMonth()->toDateString(),
]);

$byType = [];
foreach ($transactions as $txn) {
    $type = $txn->type->value;
    $byType[$type] = ($byType[$type] ?? 0) + $txn->amount;
}

echo "Spending by category:\n";
foreach ($byType as $type => $total) {
    echo "- {$type}: \$" . number_format($total, 2) . "\n";
}
```

### Fraud Detection

```php
$transactions = Motive::motiveCard()->transactions([
    'start_date' => now()->subDays(1)->toDateString(),
]);

foreach ($transactions as $txn) {
    // Flag non-fuel purchases on fuel-only cards
    if ($txn->type !== CardTransactionType::Fuel) {
        $limits = Motive::motiveCard()->limits($txn->cardId);

        if ($limits->fuelOnly) {
            Log::warning('Non-fuel purchase on fuel-only card', [
                'transaction_id' => $txn->id,
                'card_id' => $txn->cardId,
                'amount' => $txn->amount,
            ]);
        }
    }
}
```

### Driver Card Summary

```php
$drivers = Motive::users()->list(['role' => 'driver']);

foreach ($drivers as $driver) {
    $transactions = Motive::motiveCard()->transactions([
        'driver_id' => $driver->id,
        'start_date' => now()->startOfMonth()->toDateString(),
    ]);

    $total = collect($transactions)->sum('amount');
    echo "{$driver->firstName}: \$" . number_format($total, 2) . "\n";
}
```

## Related

- [Fuel Purchases](fuel-purchases.md)

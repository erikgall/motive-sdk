# Messages

The Messages resource enables communication with drivers.

## Access

```php
use Motive\Facades\Motive;

$messages = Motive::messages();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List messages |
| `paginate($page, $perPage, $params)` | Get paginated messages |
| `find($id)` | Find message by ID |
| `send($data)` | Send a message to a driver |
| `broadcast($data)` | Send to multiple drivers |

## List Messages

```php
$messages = Motive::messages()->list([
    'driver_id' => 123,
]);

foreach ($messages as $message) {
    echo "{$message->direction}: {$message->body}\n";
    echo "Sent: {$message->sentAt->format('Y-m-d H:i')}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_id` | int | Filter by driver |
| `direction` | string | `inbound` or `outbound` |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `per_page` | int | Items per page |

## Find a Message

```php
$message = Motive::messages()->find($messageId);

echo $message->body;
echo $message->direction;
```

## Send a Message

```php
$message = Motive::messages()->send([
    'driver_id' => 123,
    'body' => 'Please call dispatch when you arrive.',
]);

echo "Message sent at {$message->sentAt}";
```

### Send Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `driver_id` | int | Yes | Recipient driver ID |
| `body` | string | Yes | Message content |

## Broadcast a Message

Send to multiple drivers at once:

```php
$message = Motive::messages()->broadcast([
    'driver_ids' => [123, 456, 789],
    'body' => 'Weather alert: Severe storms expected on I-35 corridor.',
]);
```

### Broadcast Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `driver_ids` | array | Yes | List of driver IDs |
| `body` | string | Yes | Message content |

## Message DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Message ID |
| `driverId` | int | Driver ID |
| `driver` | User\|null | Driver details |
| `direction` | MessageDirection | Inbound or outbound |
| `body` | string | Message content |
| `sentAt` | CarbonImmutable | Send timestamp |
| `readAt` | CarbonImmutable\|null | Read timestamp |

## MessageDirection Enum

| Value | Description |
|-------|-------------|
| `inbound` | From driver to dispatch |
| `outbound` | From dispatch to driver |

## Use Cases

### Driver Communication Log

```php
$messages = Motive::messages()->list([
    'driver_id' => 123,
    'start_date' => now()->subDays(7)->toDateString(),
]);

echo "Communication Log:\n";
foreach ($messages as $message) {
    $arrow = $message->direction === 'inbound' ? '<-' : '->';
    echo "[{$message->sentAt->format('m/d H:i')}] {$arrow} {$message->body}\n";
}
```

### Fleet-Wide Alert

```php
$drivers = Motive::users()->list(['role' => 'driver']);
$driverIds = collect($drivers)->pluck('id')->all();

Motive::messages()->broadcast([
    'driver_ids' => $driverIds,
    'body' => 'Reminder: Submit timecards by Friday 5 PM.',
]);
```

### Unread Message Report

```php
$messages = Motive::messages()->list([
    'direction' => 'outbound',
    'start_date' => now()->subDays(1)->toDateString(),
]);

$unread = [];
foreach ($messages as $message) {
    if ($message->readAt === null) {
        $unread[] = $message;
    }
}

echo count($unread) . " unread messages\n";
```

## Related

- [Users](../drivers/users.md)
- [Documents](documents.md)

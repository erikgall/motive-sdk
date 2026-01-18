# Integration DTOs

## Webhook

Represents a webhook subscription.

```php
use Motive\Data\Webhook;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Webhook ID |
| `url` | `string` | Endpoint URL |
| `events` | `array` | Subscribed events |
| `status` | `WebhookStatus` | Current status |
| `secret` | `string\|null` | Signing secret |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## WebhookLog

Represents a webhook delivery log.

```php
use Motive\Data\WebhookLog;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Log ID |
| `webhookId` | `int` | Webhook ID |
| `event` | `string` | Event type |
| `payload` | `array\|null` | Event payload |
| `responseCode` | `int\|null` | HTTP response code |
| `responseBody` | `string\|null` | Response body |
| `success` | `bool` | Delivery success |
| `sentAt` | `CarbonImmutable` | Send timestamp |

---

## ExternalId

Represents an external ID mapping.

```php
use Motive\Data\ExternalId;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `resourceType` | `string` | Resource type |
| `resourceId` | `int` | Motive ID |
| `externalId` | `string` | External ID |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## Shipment

Represents a freight shipment.

```php
use Motive\Data\Shipment;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Shipment ID |
| `dispatchId` | `int\|null` | Dispatch ID |
| `status` | `ShipmentStatus` | Current status |
| `origin` | `string\|null` | Origin |
| `destination` | `string\|null` | Destination |
| `scheduledPickup` | `CarbonImmutable\|null` | Scheduled pickup |
| `scheduledDelivery` | `CarbonImmutable\|null` | Scheduled delivery |
| `actualPickup` | `CarbonImmutable\|null` | Actual pickup |
| `actualDelivery` | `CarbonImmutable\|null` | Actual delivery |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |

---

## ShipmentTracking

Represents shipment position.

```php
use Motive\Data\ShipmentTracking;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `shipmentId` | `int` | Shipment ID |
| `latitude` | `float` | Current latitude |
| `longitude` | `float` | Current longitude |
| `speed` | `float\|null` | Current speed |
| `heading` | `int\|null` | Heading |
| `updatedAt` | `CarbonImmutable` | Last update |
| `history` | `array\|null` | Position history |

---

## ShipmentEta

Represents estimated arrival.

```php
use Motive\Data\ShipmentEta;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `shipmentId` | `int` | Shipment ID |
| `destination` | `string` | Destination |
| `estimatedArrival` | `CarbonImmutable` | ETA |
| `distanceRemaining` | `float` | Miles remaining |
| `timeRemaining` | `int` | Minutes remaining |
| `status` | `string` | ETA status |
| `confidence` | `float\|null` | Confidence level |
| `calculatedAt` | `CarbonImmutable` | Calculation time |

---

## ReeferActivity

Represents refrigerated trailer data.

```php
use Motive\Data\ReeferActivity;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Record ID |
| `vehicleId` | `int\|null` | Vehicle ID |
| `assetId` | `int\|null` | Asset ID |
| `temperature` | `float` | Temperature |
| `setpoint` | `float\|null` | Target temp |
| `mode` | `string\|null` | Operating mode |
| `fuelLevel` | `float\|null` | Fuel level % |
| `engineHours` | `float\|null` | Engine hours |
| `alarms` | `array\|null` | Active alarms |
| `recordedAt` | `CarbonImmutable` | Recording time |

## Related

- [Webhooks Resource](../api-reference/integration/webhooks.md)
- [External IDs Resource](../api-reference/integration/external-ids.md)
- [WebhookStatus Enum](../enums/webhook-enums.md)
- [ShipmentStatus Enum](../enums/status-enums.md)

# Communication DTOs

## Message

Represents a driver message.

```php
use Motive\Data\Message;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Message ID |
| `driverId` | `int` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `direction` | `MessageDirection` | Inbound or outbound |
| `body` | `string` | Message content |
| `sentAt` | `CarbonImmutable` | Send timestamp |
| `readAt` | `CarbonImmutable\|null` | Read timestamp |

### Example

```php
$messages = Motive::messages()->list(['driver_id' => 123]);

foreach ($messages as $message) {
    $arrow = $message->direction === MessageDirection::Inbound ? '<-' : '->';
    echo "[{$message->sentAt->format('H:i')}] {$arrow} {$message->body}\n";
}
```

---

## Document

Represents a document record.

```php
use Motive\Data\Document;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Document ID |
| `name` | `string` | Document name |
| `type` | `DocumentType` | Document type |
| `status` | `DocumentStatus` | Current status |
| `driverId` | `int\|null` | Driver ID |
| `driver` | `User\|null` | Driver details |
| `dispatchId` | `int\|null` | Dispatch ID |
| `fileSize` | `int\|null` | Size in bytes |
| `mimeType` | `string\|null` | MIME type |
| `notes` | `string\|null` | Notes |
| `images` | `array\|null` | Document images |
| `createdAt` | `CarbonImmutable\|null` | Upload timestamp |

---

## DocumentImage

Represents a document image.

```php
use Motive\Data\DocumentImage;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int\|null` | Image ID |
| `url` | `string\|null` | Image URL |
| `thumbnailUrl` | `string\|null` | Thumbnail URL |
| `pageNumber` | `int\|null` | Page number |
| `width` | `int\|null` | Width in pixels |
| `height` | `int\|null` | Height in pixels |

## Related

- [Messages Resource](../api-reference/communication/messages.md)
- [Documents Resource](../api-reference/communication/documents.md)
- [MessageDirection Enum](../enums/type-enums.md)
- [DocumentType Enum](../enums/type-enums.md)
- [DocumentStatus Enum](../enums/status-enums.md)

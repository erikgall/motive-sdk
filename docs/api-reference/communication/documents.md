# Documents

The Documents resource manages document uploads, downloads, and tracking.

## Access

```php
use Motive\Facades\Motive;

$documents = Motive::documents();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List documents |
| `paginate($page, $perPage, $params)` | Get paginated documents |
| `find($id)` | Find document by ID |
| `upload($data)` | Upload a document |
| `download($id)` | Download document content |
| `delete($id)` | Delete a document |
| `updateStatus($id, $status)` | Update document status |

## List Documents

```php
$documents = Motive::documents()->list([
    'driver_id' => 123,
    'status' => 'pending',
]);

foreach ($documents as $document) {
    echo "{$document->name}\n";
    echo "Type: {$document->type}\n";
    echo "Status: {$document->status->value}\n";
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `driver_id` | int | Filter by driver |
| `status` | string | Filter by status |
| `type` | string | Filter by document type |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `per_page` | int | Items per page |

## Find a Document

```php
$document = Motive::documents()->find($documentId);

echo $document->name;
echo $document->type;
echo $document->status->value;
```

## Upload a Document

```php
$document = Motive::documents()->upload([
    'driver_id' => 123,
    'name' => 'Bill of Lading - Order 12345',
    'type' => 'bill_of_lading',
    'file' => fopen('/path/to/document.pdf', 'r'),
]);

echo "Uploaded document #{$document->id}";
```

### Upload Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `driver_id` | int | No | Associated driver ID |
| `name` | string | Yes | Document name |
| `type` | string | Yes | Document type |
| `file` | resource | Yes | File handle |
| `dispatch_id` | int | No | Associated dispatch ID |
| `notes` | string | No | Additional notes |

## Download a Document

```php
$content = Motive::documents()->download($documentId);

// Save to file
file_put_contents('downloaded-document.pdf', $content);

// Or return as download response
return response($content)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="document.pdf"');
```

## Delete a Document

```php
$deleted = Motive::documents()->delete($documentId);
```

## Update Document Status

```php
use Motive\Enums\DocumentStatus;

Motive::documents()->updateStatus($documentId, DocumentStatus::Approved);
```

## Document DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Document ID |
| `name` | string | Document name |
| `type` | DocumentType | Document type |
| `status` | DocumentStatus | Current status |
| `driverId` | int\|null | Driver ID |
| `driver` | User\|null | Driver details |
| `dispatchId` | int\|null | Dispatch ID |
| `fileSize` | int\|null | File size in bytes |
| `mimeType` | string\|null | MIME type |
| `notes` | string\|null | Notes |
| `images` | array\|null | Document images |
| `createdAt` | CarbonImmutable\|null | Upload timestamp |

## DocumentType Enum

| Value | Description |
|-------|-------------|
| `bill_of_lading` | Bill of Lading |
| `proof_of_delivery` | Proof of Delivery |
| `receipt` | Receipt |
| `inspection` | Inspection document |
| `other` | Other document type |

## DocumentStatus Enum

| Value | Description |
|-------|-------------|
| `pending` | Awaiting review |
| `approved` | Approved |
| `rejected` | Rejected |

## Use Cases

### Document Review Queue

```php
$pending = Motive::documents()->list([
    'status' => 'pending',
]);

foreach ($pending as $document) {
    echo "#{$document->id}: {$document->name}\n";
    echo "Type: {$document->type}\n";
    echo "Uploaded: {$document->createdAt->diffForHumans()}\n";
}
```

### Delivery Documentation

```php
// Get all PODs for a dispatch
$documents = Motive::documents()->list([
    'dispatch_id' => $dispatchId,
    'type' => 'proof_of_delivery',
]);

foreach ($documents as $doc) {
    $content = Motive::documents()->download($doc->id);
    file_put_contents("pod-{$doc->id}.pdf", $content);
}
```

## Related

- [Messages](messages.md)
- [Dispatches](../dispatch/dispatches.md)
- [DocumentType Enum](../../enums/type-enums.md)
- [DocumentStatus Enum](../../enums/status-enums.md)

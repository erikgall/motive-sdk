# Pagination

The Motive SDK provides two pagination methods: lazy pagination for memory-efficient iteration and standard pagination for explicit page control.

## Lazy Pagination

Lazy pagination is the recommended approach for most use cases. It fetches pages on-demand as you iterate, making it memory-efficient for large datasets.

### Basic Usage

```php
use Motive\Facades\Motive;

$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    // Pages are fetched automatically as needed
    echo "{$vehicle->number}\n";
}
```

### With Filters

```php
$vehicles = Motive::vehicles()->list([
    'status' => 'active',
    'per_page' => 50,
]);

foreach ($vehicles as $vehicle) {
    echo $vehicle->number;
}
```

### Converting to Array

If you need all items at once (use with caution for large datasets):

```php
$vehicles = Motive::vehicles()->list()->all();
// Returns array of all Vehicle DTOs
```

### Collection Methods

The lazy collection supports all Laravel collection methods:

```php
$vehicles = Motive::vehicles()->list();

// Filter
$active = $vehicles->filter(fn ($v) => $v->status === VehicleStatus::Active);

// Map
$numbers = $vehicles->map(fn ($v) => $v->number);

// Take first N
$firstTen = $vehicles->take(10);

// Count (fetches all pages)
$count = $vehicles->count();
```

## Standard Pagination

Standard pagination gives you explicit control over page navigation and access to pagination metadata.

### Basic Usage

```php
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);

// Access metadata
echo "Total items: {$page->total()}";
echo "Per page: {$page->perPage()}";
echo "Current page: {$page->currentPage()}";
echo "Last page: {$page->lastPage()}";
echo "Items on this page: {$page->count()}";

// Iterate items
foreach ($page->items() as $vehicle) {
    echo $vehicle->number;
}
```

### Pagination Metadata

| Method | Description |
|--------|-------------|
| `total()` | Total number of items across all pages |
| `perPage()` | Items per page |
| `currentPage()` | Current page number |
| `lastPage()` | Last page number |
| `count()` | Items on current page |
| `hasMorePages()` | Whether more pages exist |
| `items()` | Array of DTOs for current page |

### Manual Page Navigation

```php
$page = Motive::vehicles()->paginate(page: 1);

while ($page->hasMorePages()) {
    foreach ($page->items() as $vehicle) {
        processVehicle($vehicle);
    }

    $page = Motive::vehicles()->paginate(
        page: $page->currentPage() + 1
    );
}
```

### With Filters

```php
$page = Motive::vehicles()->paginate(
    page: 1,
    perPage: 50,
    params: [
        'status' => 'active',
        'sort' => 'number',
    ]
);
```

## Choosing Between Methods

### Use Lazy Pagination When:

- Processing all items sequentially
- Memory efficiency is important
- You don't need pagination metadata
- Building exports or reports

```php
// Good: Memory-efficient processing
$vehicles = Motive::vehicles()->list();
foreach ($vehicles as $vehicle) {
    exportToFile($vehicle);
}
```

### Use Standard Pagination When:

- Building paginated UI
- Need total count upfront
- Need random page access
- Displaying pagination controls

```php
// Good: Building paginated API response
$page = Motive::vehicles()->paginate(page: $request->page);

return response()->json([
    'data' => $page->items(),
    'meta' => [
        'total' => $page->total(),
        'current_page' => $page->currentPage(),
        'last_page' => $page->lastPage(),
    ],
]);
```

## Pagination Parameters

Most resources accept these pagination parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 25 | Items per page (max varies by endpoint) |

## Performance Considerations

### Lazy Pagination

- **Memory**: Only keeps current page in memory
- **Network**: Makes one request per page as you iterate
- **Best for**: Large datasets, sequential processing

### Standard Pagination

- **Memory**: Holds one page in memory
- **Network**: One request per page, explicit control
- **Best for**: UI pagination, random access

### Tips

1. **Use appropriate page size**: Larger pages mean fewer requests but more memory per request
2. **Avoid `count()` on lazy collections**: It fetches all pages to count
3. **Use `take()` for limits**: `$vehicles->take(100)` stops after 100 items
4. **Filter server-side**: Pass filter parameters to reduce data transfer

```php
// Efficient: Server-side filtering
$active = Motive::vehicles()->list(['status' => 'active']);

// Less efficient: Client-side filtering
$active = Motive::vehicles()->list()
    ->filter(fn ($v) => $v->status === VehicleStatus::Active);
```

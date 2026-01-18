# Form Entries

The Form Entries resource retrieves submitted form data from drivers.

## Access

```php
use Motive\Facades\Motive;

$formEntries = Motive::formEntries();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List form entries |
| `find($id)` | Find form entry by ID |

## List Form Entries

```php
$entries = Motive::formEntries()->list([
    'form_id' => $formId,
    'start_date' => now()->subDays(7)->toDateString(),
]);

foreach ($entries as $entry) {
    echo "Form: {$entry->formId}\n";
    echo "Submitted by: {$entry->driverId}\n";
    echo "Date: {$entry->submittedAt}\n";

    foreach ($entry->values as $field => $value) {
        echo "  {$field}: {$value}\n";
    }
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `form_id` | int | Filter by form template |
| `driver_id` | int | Filter by driver |
| `start_date` | string | Start date (YYYY-MM-DD) |
| `end_date` | string | End date (YYYY-MM-DD) |
| `per_page` | int | Items per page |

## Find a Form Entry

```php
$entry = Motive::formEntries()->find($entryId);

echo "Form ID: {$entry->formId}";
echo "Driver: {$entry->driverId}";
echo "Submitted: {$entry->submittedAt}";

foreach ($entry->values as $field => $value) {
    echo "{$field}: {$value}\n";
}
```

## FormEntry DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Entry ID |
| `formId` | int | Form template ID |
| `driverId` | int | Submitting driver ID |
| `driver` | User\|null | Driver details |
| `values` | array | Field values |
| `submittedAt` | CarbonImmutable | Submission time |
| `latitude` | float\|null | Submission location |
| `longitude` | float\|null | Submission location |

## Use Cases

### Daily Form Submissions Report

```php
$entries = Motive::formEntries()->list([
    'start_date' => now()->toDateString(),
]);

$byForm = collect($entries)->groupBy('formId');

foreach ($byForm as $formId => $formEntries) {
    echo "Form {$formId}: " . count($formEntries) . " submissions\n";
}
```

### Driver Compliance Check

```php
$drivers = Motive::users()->list(['role' => 'driver'])->all();
$entries = Motive::formEntries()->list([
    'form_id' => $requiredFormId,
    'start_date' => now()->toDateString(),
]);

$submittedDriverIds = collect($entries)->pluck('driverId')->unique();

$missing = [];
foreach ($drivers as $driver) {
    if (! $submittedDriverIds->contains($driver->id)) {
        $missing[] = $driver;
    }
}

echo count($missing) . " drivers haven't submitted today's form";
```

### Export Form Data

```php
$entries = Motive::formEntries()->list([
    'form_id' => $formId,
    'start_date' => now()->startOfMonth()->toDateString(),
]);

$data = [];
foreach ($entries as $entry) {
    $data[] = array_merge(
        ['driver_id' => $entry->driverId, 'submitted_at' => $entry->submittedAt],
        $entry->values
    );
}

// Export to CSV or database
```

## Related

- [Forms](forms.md)
- [Users](../drivers/users.md)

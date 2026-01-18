# Forms

The Forms resource retrieves custom form templates defined in your Motive account.

## Access

```php
use Motive\Facades\Motive;

$forms = Motive::forms();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List form templates |

## List Forms

```php
$forms = Motive::forms()->list();

foreach ($forms as $form) {
    echo "{$form->name}\n";
    echo "Fields: " . count($form->fields ?? []) . "\n";

    foreach ($form->fields as $field) {
        echo "  - {$field->name} ({$field->type})\n";
    }
}
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | int | Items per page |

## Form DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Form ID |
| `name` | string | Form name |
| `description` | string\|null | Description |
| `fields` | array\|null | Form fields |
| `active` | bool | Whether form is active |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## FormField DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Field ID |
| `name` | string | Field name |
| `type` | FormFieldType | Field type |
| `required` | bool | Whether required |
| `options` | array\|null | Options for select fields |
| `order` | int\|null | Display order |

## FormFieldType Enum

| Value | Description |
|-------|-------------|
| `text` | Text input |
| `number` | Numeric input |
| `date` | Date picker |
| `time` | Time picker |
| `select` | Dropdown selection |
| `checkbox` | Checkbox |
| `signature` | Signature capture |
| `photo` | Photo attachment |

## Use Cases

### Form Directory

```php
$forms = Motive::forms()->list();

$activeForms = [];
foreach ($forms as $form) {
    if ($form->active) {
        $activeForms[] = $form;
    }
}

echo count($activeForms) . " active forms available\n";
```

### Form Field Analysis

```php
$forms = Motive::forms()->list();

foreach ($forms as $form) {
    $required = collect($form->fields ?? [])
        ->where('required', true)
        ->count();

    echo "{$form->name}: {$required} required fields\n";
}
```

## Related

- [Form Entries](form-entries.md)

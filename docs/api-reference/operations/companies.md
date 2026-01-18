# Companies

The Companies resource provides information about your Motive account.

## Access

```php
use Motive\Facades\Motive;

$companies = Motive::companies();
```

## Methods

| Method | Description |
|--------|-------------|
| `current()` | Get current company information |

## Get Current Company

```php
$company = Motive::companies()->current();

echo "Company: {$company->name}\n";
echo "DOT Number: {$company->dotNumber}\n";
echo "MC Number: {$company->mcNumber}\n";
echo "Address: {$company->address}\n";
```

## Company DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Company ID |
| `name` | string | Company name |
| `dotNumber` | string\|null | DOT number |
| `mcNumber` | string\|null | MC number |
| `address` | string\|null | Street address |
| `city` | string\|null | City |
| `state` | string\|null | State |
| `postalCode` | string\|null | Postal code |
| `country` | string\|null | Country |
| `phone` | string\|null | Phone number |
| `email` | string\|null | Contact email |
| `timezone` | string\|null | Default timezone |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## Use Cases

### Configuration Verification

```php
$company = Motive::companies()->current();

if (! $company->dotNumber) {
    Log::warning('DOT number not configured');
}

if (! $company->timezone) {
    Log::warning('Timezone not configured');
}
```

### Multi-Tenant Display

```php
// In multi-tenant app, show company info
$company = Motive::withApiKey($tenant->motive_api_key)
    ->companies()
    ->current();

return view('dashboard', [
    'companyName' => $company->name,
    'dotNumber' => $company->dotNumber,
]);
```

## Related

- [Multi-Tenancy](../../authentication/multi-tenancy.md)

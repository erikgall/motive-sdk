# Users

The Users resource manages drivers and staff in your organization.

## Access

```php
use Motive\Facades\Motive;

$users = Motive::users();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List all users |
| `paginate($page, $perPage, $params)` | Get paginated users |
| `find($id)` | Find user by ID |
| `findByExternalId($externalId)` | Find user by external ID |
| `create($data)` | Create a new user |
| `update($id, $data)` | Update a user |
| `delete($id)` | Delete a user |
| `deactivate($id)` | Deactivate a user |
| `reactivate($id)` | Reactivate a user |

## List Users

```php
$users = Motive::users()->list();

foreach ($users as $user) {
    echo "{$user->firstName} {$user->lastName}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
}

// Filter by role
$drivers = Motive::users()->list(['role' => 'driver']);
```

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `role` | string | Filter by role: `driver`, `admin`, etc. |
| `status` | string | Filter by status |
| `per_page` | int | Items per page |

## Find a User

```php
$user = Motive::users()->find(456);

echo "{$user->firstName} {$user->lastName}";
echo $user->email;

// Access driver-specific data
if ($user->driver) {
    echo "Driver License: {$user->driver->licenseNumber}";
    echo "License State: {$user->driver->licenseState}";
}
```

## Create a User

```php
$user = Motive::users()->create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '555-123-4567',
    'role' => 'driver',
    'driver' => [
        'license_number' => 'DL123456',
        'license_state' => 'TX',
    ],
]);
```

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `first_name` | string | Yes | First name |
| `last_name` | string | Yes | Last name |
| `email` | string | Yes | Email address |
| `phone` | string | No | Phone number |
| `role` | string | Yes | User role |
| `driver` | array | No | Driver-specific data |
| `external_id` | string | No | External system ID |

### Driver Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `license_number` | string | Driver's license number |
| `license_state` | string | License issuing state |
| `license_expiration` | string | License expiration date |

## Update a User

```php
$user = Motive::users()->update(456, [
    'phone' => '555-987-6543',
]);
```

## Delete a User

```php
$deleted = Motive::users()->delete(456);
```

## Deactivate a User

Temporarily disable a user account:

```php
Motive::users()->deactivate(456);
echo "User deactivated";
```

## Reactivate a User

Re-enable a deactivated user:

```php
Motive::users()->reactivate(456);
echo "User reactivated";
```

## User DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | User ID |
| `firstName` | string | First name |
| `lastName` | string | Last name |
| `email` | string | Email address |
| `phone` | string\|null | Phone number |
| `role` | string | User role |
| `status` | UserStatus | Account status |
| `driver` | Driver\|null | Driver details |
| `externalId` | string\|null | External ID |
| `createdAt` | CarbonImmutable\|null | Created timestamp |
| `updatedAt` | CarbonImmutable\|null | Updated timestamp |

## Driver DTO

| Property | Type | Description |
|----------|------|-------------|
| `licenseNumber` | string\|null | License number |
| `licenseState` | string\|null | License state |
| `licenseExpiration` | CarbonImmutable\|null | License expiration |
| `eldExempt` | bool | ELD exemption status |

## Related

- [User DTO Reference](../../dto-reference/drivers.md)
- [UserRole Enum](../../enums/type-enums.md)
- [UserStatus Enum](../../enums/status-enums.md)

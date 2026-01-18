# Driver DTOs

## User

Represents a user (driver or staff member).

```php
use Motive\Data\User;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | User ID |
| `firstName` | `string` | First name |
| `lastName` | `string` | Last name |
| `email` | `string` | Email address |
| `phone` | `string\|null` | Phone number |
| `role` | `string` | User role |
| `status` | `UserStatus` | Account status |
| `driver` | `Driver\|null` | Driver-specific data |
| `externalId` | `string\|null` | External ID |
| `timezone` | `string\|null` | User timezone |
| `createdAt` | `CarbonImmutable\|null` | Created timestamp |
| `updatedAt` | `CarbonImmutable\|null` | Updated timestamp |

### Example

```php
$user = Motive::users()->find(123);

echo "{$user->firstName} {$user->lastName}";
echo $user->email;
echo $user->role;

if ($user->driver) {
    echo "License: {$user->driver->licenseNumber}";
}
```

---

## Driver

Represents driver-specific information (nested in User).

```php
use Motive\Data\Driver;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `licenseNumber` | `string\|null` | Driver's license number |
| `licenseState` | `string\|null` | License issuing state |
| `licenseExpiration` | `CarbonImmutable\|null` | License expiration date |
| `eldExempt` | `bool` | ELD exemption status |
| `medicalCertExpiration` | `CarbonImmutable\|null` | Medical cert expiration |
| `hazmatEndorsement` | `bool\|null` | Hazmat endorsement |
| `tankerEndorsement` | `bool\|null` | Tanker endorsement |

### Example

```php
$user = Motive::users()->find(123);

if ($user->driver) {
    echo "License: {$user->driver->licenseNumber}";
    echo "State: {$user->driver->licenseState}";
    echo "Expires: {$user->driver->licenseExpiration->format('Y-m-d')}";
    echo "ELD Exempt: " . ($user->driver->eldExempt ? 'Yes' : 'No');
}
```

---

## DrivingPeriod

Represents a driver's driving activity period.

```php
use Motive\Data\DrivingPeriod;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `int` | Period ID |
| `driverId` | `int` | Driver ID |
| `vehicleId` | `int\|null` | Vehicle ID |
| `startTime` | `CarbonImmutable` | Period start |
| `endTime` | `CarbonImmutable\|null` | Period end |
| `duration` | `int` | Duration in minutes |
| `distance` | `float\|null` | Distance traveled |
| `startLocation` | `string\|null` | Starting location |
| `endLocation` | `string\|null` | Ending location |

---

## Scorecard

Represents a driver's safety scorecard.

```php
use Motive\Data\Scorecard;
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `driverId` | `int\|null` | Driver ID |
| `overallScore` | `float` | Overall safety score (0-100) |
| `harshBrakingScore` | `float\|null` | Harsh braking score |
| `harshAccelerationScore` | `float\|null` | Harsh acceleration score |
| `speedingScore` | `float\|null` | Speeding score |
| `corneringScore` | `float\|null` | Cornering score |
| `totalMiles` | `float` | Total miles in period |
| `totalEvents` | `int\|null` | Total safety events |
| `periodStart` | `CarbonImmutable` | Period start date |
| `periodEnd` | `CarbonImmutable` | Period end date |

### Example

```php
$scorecard = Motive::scorecard()->forDriver(123, [
    'start_date' => now()->subDays(30)->toDateString(),
    'end_date' => now()->toDateString(),
]);

echo "Overall: {$scorecard->overallScore}/100";
echo "Miles: {$scorecard->totalMiles}";
echo "Events: {$scorecard->totalEvents}";
```

## Related

- [Users Resource](../api-reference/drivers/users.md)
- [Scorecard Resource](../api-reference/drivers/scorecard.md)
- [UserStatus Enum](../enums/status-enums.md)

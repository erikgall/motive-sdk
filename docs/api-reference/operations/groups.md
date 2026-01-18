# Groups

The Groups resource manages organizational units for drivers, vehicles, and assets.

## Access

```php
use Motive\Facades\Motive;

$groups = Motive::groups();
```

## Methods

| Method | Description |
|--------|-------------|
| `list($params)` | List groups |
| `paginate($page, $perPage, $params)` | Get paginated groups |
| `find($id)` | Find group by ID |
| `create($data)` | Create a group |
| `update($id, $data)` | Update a group |
| `delete($id)` | Delete a group |
| `addMember($groupId, $memberId, $type)` | Add member to group |
| `removeMember($groupId, $memberId, $type)` | Remove member from group |

## List Groups

```php
$groups = Motive::groups()->list();

foreach ($groups as $group) {
    echo "{$group->name}\n";
    echo "Members: " . count($group->members ?? []) . "\n";
}
```

## Find a Group

```php
$group = Motive::groups()->find($groupId);

echo $group->name;

foreach ($group->members as $member) {
    echo "- {$member->type}: {$member->memberId}\n";
}
```

## Create a Group

```php
$group = Motive::groups()->create([
    'name' => 'Southwest Region',
    'description' => 'Drivers operating in TX, NM, AZ',
]);
```

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Group name |
| `description` | string | No | Description |
| `external_id` | string | No | External system ID |

## Update a Group

```php
$group = Motive::groups()->update($groupId, [
    'name' => 'Southwest Region - Updated',
]);
```

## Delete a Group

```php
$deleted = Motive::groups()->delete($groupId);
```

## Add Member to Group

```php
// Add a driver
Motive::groups()->addMember($groupId, $driverId, 'driver');

// Add a vehicle
Motive::groups()->addMember($groupId, $vehicleId, 'vehicle');
```

## Remove Member from Group

```php
Motive::groups()->removeMember($groupId, $driverId, 'driver');
```

## Group DTO

| Property | Type | Description |
|----------|------|-------------|
| `id` | int | Group ID |
| `name` | string | Group name |
| `description` | string\|null | Description |
| `members` | array\|null | Group members |
| `externalId` | string\|null | External ID |
| `createdAt` | CarbonImmutable\|null | Created timestamp |

## GroupMember DTO

| Property | Type | Description |
|----------|------|-------------|
| `memberId` | int | Member ID |
| `type` | string | Member type (driver, vehicle) |
| `addedAt` | CarbonImmutable\|null | When added |

## Use Cases

### Regional Organization

```php
$regions = [
    'Northeast' => ['NY', 'NJ', 'PA'],
    'Southwest' => ['TX', 'NM', 'AZ'],
];

foreach ($regions as $name => $states) {
    Motive::groups()->create([
        'name' => $name,
        'description' => 'States: ' . implode(', ', $states),
    ]);
}
```

### Group Membership Report

```php
$groups = Motive::groups()->list();

foreach ($groups as $group) {
    $drivers = collect($group->members ?? [])
        ->where('type', 'driver')
        ->count();

    $vehicles = collect($group->members ?? [])
        ->where('type', 'vehicle')
        ->count();

    echo "{$group->name}: {$drivers} drivers, {$vehicles} vehicles\n";
}
```

## Related

- [Users](../drivers/users.md)
- [Vehicles](../fleet/vehicles.md)

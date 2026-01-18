# Scope Enums

## Scope

OAuth permission scopes for API access.

```php
use Motive\Enums\Scope;
```

### Read Scopes

| Case | Value | Description |
|------|-------|-------------|
| `VehiclesRead` | `vehicles.read` | Read vehicle data |
| `UsersRead` | `users.read` | Read user data |
| `HosRead` | `hos.read` | Read HOS data |
| `DispatchesRead` | `dispatches.read` | Read dispatches |
| `AssetsRead` | `assets.read` | Read assets |
| `LocationsRead` | `locations.read` | Read locations |
| `GroupsRead` | `groups.read` | Read groups |
| `DocumentsRead` | `documents.read` | Read documents |
| `MessagesRead` | `messages.read` | Read messages |
| `FormsRead` | `forms.read` | Read forms |
| `TimecardsRead` | `timecards.read` | Read timecards |
| `SafetyRead` | `safety.read` | Read safety data |
| `ReportsRead` | `reports.read` | Read reports |
| `WebhooksRead` | `webhooks.read` | Read webhooks |

### Write Scopes

| Case | Value | Description |
|------|-------|-------------|
| `VehiclesWrite` | `vehicles.write` | Create/update vehicles |
| `UsersWrite` | `users.write` | Create/update users |
| `HosWrite` | `hos.write` | Create/edit HOS logs |
| `DispatchesWrite` | `dispatches.write` | Create/update dispatches |
| `AssetsWrite` | `assets.write` | Create/update assets |
| `LocationsWrite` | `locations.write` | Create/update locations |
| `GroupsWrite` | `groups.write` | Create/update groups |
| `DocumentsWrite` | `documents.write` | Upload documents |
| `MessagesWrite` | `messages.write` | Send messages |
| `TimecardsWrite` | `timecards.write` | Update timecards |
| `WebhooksWrite` | `webhooks.write` | Manage webhooks |

### Usage

```php
use Motive\Enums\Scope;

// Generate OAuth URL with specific scopes
$url = Motive::oauth()->authorizationUrl(
    scopes: [
        Scope::VehiclesRead,
        Scope::UsersRead,
        Scope::HosRead,
        Scope::DispatchesRead,
        Scope::DispatchesWrite,
    ],
    state: $state,
);

return redirect($url);
```

### Scope Selection Guide

**For read-only integration:**
```php
$readOnlyScopes = [
    Scope::VehiclesRead,
    Scope::UsersRead,
    Scope::HosRead,
    Scope::DispatchesRead,
    Scope::SafetyRead,
];
```

**For dispatch management:**
```php
$dispatchScopes = [
    Scope::VehiclesRead,
    Scope::UsersRead,
    Scope::DispatchesRead,
    Scope::DispatchesWrite,
    Scope::LocationsRead,
    Scope::LocationsWrite,
    Scope::MessagesRead,
    Scope::MessagesWrite,
];
```

**For compliance monitoring:**
```php
$complianceScopes = [
    Scope::UsersRead,
    Scope::VehiclesRead,
    Scope::HosRead,
    Scope::SafetyRead,
    Scope::ReportsRead,
];
```

### Best Practices

1. **Request minimum scopes** - Only request what you need
2. **Separate read/write** - Request write scopes only when necessary
3. **Document scope usage** - Explain why each scope is needed
4. **Review periodically** - Remove unused scopes over time

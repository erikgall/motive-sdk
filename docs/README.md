# Motive SDK

A Laravel SDK for the [Motive ELD API](https://developer.gomotive.com/) with full coverage of fleet management, HOS compliance, dispatch, and safety endpoints.

## Installation

```bash
composer require erikgall/motive-sdk
```

Add your API key to `.env`:

```env
MOTIVE_API_KEY=your-api-key
```

## Basic Usage

```php
use Motive\Facades\Motive;

// List vehicles
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}

// Get driver HOS availability
$availability = Motive::hosAvailability()->forDriver($driverId);

// Create a dispatch
$dispatch = Motive::dispatches()->create([
    'driver_id' => $driverId,
    'vehicle_id' => $vehicleId,
]);
```

## Requirements

- PHP 8.2+
- Laravel 11+

## Features

- **31 API Resources** — Vehicles, drivers, HOS, dispatch, safety, documents, and more
- **Type-safe DTOs** — 50+ data objects with automatic casting for dates, enums, and nested objects
- **Lazy pagination** — Memory-efficient iteration over large datasets
- **Dual authentication** — API key and OAuth 2.0 with automatic token refresh
- **Multi-tenancy** — Named connections for multiple Motive accounts
- **Testing utilities** — Built-in faking, factories, and assertions

## Documentation

| Section | Description |
|---------|-------------|
| [Installation](/getting-started/installation.md) | Install and configure the SDK |
| [Configuration](/getting-started/configuration.md) | Environment and config options |
| [Quick Start](/getting-started/quick-start.md) | Common usage patterns |
| [API Reference](/api-reference/README.md) | All 31 resources with examples |
| [Authentication](/authentication/api-key.md) | API key and OAuth setup |
| [Error Handling](/core-concepts/error-handling.md) | Exception types and handling |
| [Testing](/testing/README.md) | Faking, factories, assertions |

## Links

- [GitHub Repository](https://github.com/erikgall/motive-sdk)
- [Motive API Documentation](https://developer.gomotive.com/)
- [Packagist](https://packagist.org/packages/erikgall/motive-sdk)

# Motive SDK

A first-party-quality Laravel SDK for the [Motive ELD API](https://developer.gomotive.com/) featuring expressive, elegant syntax with fluent, chainable methods.

## Features

- **Expressive Syntax** - Fluent, chainable methods that feel native to Laravel
- **31 API Resources** - Full coverage of the Motive API
- **50 Data Transfer Objects** - Strongly-typed DTOs with automatic casting
- **26 Enums** - Type-safe status and type values
- **Dual Authentication** - Support for both API key and OAuth authentication
- **Lazy Pagination** - Memory-efficient iteration over large datasets
- **Comprehensive Testing** - Built-in faking, factories, and assertions
- **Context Modifiers** - Timezone, metric units, and user context support
- **Multi-Tenancy** - Named connections for managing multiple accounts

## Requirements

- PHP 8.2+
- Laravel 11+

## Quick Example

```php
use Motive\Facades\Motive;

// List all vehicles with lazy pagination
$vehicles = Motive::vehicles()->list();

foreach ($vehicles as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}

// Get HOS availability for a driver
$availability = Motive::hosAvailability()->forDriver(123);
echo "Drive time remaining: {$availability->driveTimeRemaining} minutes";

// Create a dispatch with stops
$dispatch = Motive::dispatches()->create([
    'external_id' => 'ORDER-12345',
    'driver_id' => 123,
    'vehicle_id' => 456,
]);
```

## Documentation Sections

### [Getting Started](getting-started/installation.md)
Installation, configuration, and your first API calls.

### [Core Concepts](core-concepts/architecture.md)
Understanding the SDK architecture, resources, DTOs, and error handling.

### [Authentication](authentication/api-key.md)
API key authentication, OAuth flows, and multi-tenant support.

### [API Reference](api-reference/README.md)
Complete reference for all 31 API resources with examples.

### [DTO Reference](dto-reference/README.md)
Documentation for all 50 data transfer objects.

### [Enums](enums/README.md)
Reference for all 26 enum types.

### [Webhooks](webhooks/README.md)
Setting up and handling Motive webhooks.

### [Testing](testing/README.md)
Testing utilities including faking, factories, and assertions.

### [Advanced](advanced/raw-requests.md)
Raw API requests, macros, and advanced customization.

## Support

- [GitHub Issues](https://github.com/erikgall/motive-sdk/issues) - Bug reports and feature requests
- [Motive API Documentation](https://developer.gomotive.com/) - Official API reference

## License

The Motive SDK is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

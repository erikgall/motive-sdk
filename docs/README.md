# Motive SDK

A first-party-quality Laravel SDK for the [Motive ELD API](https://developer.gomotive.com/).

<div class="quick-links">
  <a href="#/getting-started/installation">Get Started</a>
  <a href="#/api-reference/README" class="secondary">API Reference</a>
  <a href="https://github.com/erikgall/motive-sdk" class="secondary">GitHub</a>
</div>

## Why Motive SDK?

Build fleet management integrations with clean, expressive code that feels native to Laravel.

```php
use Motive\Facades\Motive;

// List vehicles with automatic pagination
foreach (Motive::vehicles()->list() as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}";
}

// Check driver HOS availability
$availability = Motive::hosAvailability()->forDriver($driverId);
echo "Drive time remaining: {$availability->driveTimeRemaining} minutes";
```

## Features

<div class="feature-grid">
  <div class="feature-card">
    <h3>31 API Resources</h3>
    <p>Complete coverage of vehicles, drivers, HOS, dispatch, safety, and more.</p>
  </div>
  <div class="feature-card">
    <h3>Type-Safe DTOs</h3>
    <p>50+ data objects with automatic casting for dates, enums, and nested objects.</p>
  </div>
  <div class="feature-card">
    <h3>Lazy Pagination</h3>
    <p>Memory-efficient iteration over large datasets using Laravel collections.</p>
  </div>
  <div class="feature-card">
    <h3>Dual Authentication</h3>
    <p>Support for both API key and OAuth 2.0 with automatic token refresh.</p>
  </div>
  <div class="feature-card">
    <h3>Multi-Tenancy</h3>
    <p>Named connections for managing multiple Motive accounts in one app.</p>
  </div>
  <div class="feature-card">
    <h3>Testing Utilities</h3>
    <p>Built-in faking, factories, and assertions for comprehensive testing.</p>
  </div>
</div>

## Requirements

- PHP 8.2+
- Laravel 11+

## Quick Install

```bash
composer require erikgall/motive-sdk
```

```env
MOTIVE_API_KEY=your-api-key
```

## Documentation

| Section | Description |
|---------|-------------|
| [Getting Started](/getting-started/installation.md) | Installation, configuration, first API calls |
| [Core Concepts](/core-concepts/architecture.md) | Architecture, resources, DTOs, error handling |
| [Authentication](/authentication/api-key.md) | API key, OAuth, multi-tenant setup |
| [API Reference](/api-reference/README.md) | All 31 resources with examples |
| [Webhooks](/webhooks/README.md) | Receiving real-time events |
| [Testing](/testing/README.md) | Faking, factories, assertions |

## Support

- [GitHub Issues](https://github.com/erikgall/motive-sdk/issues) — Bug reports and feature requests
- [Motive API Docs](https://developer.gomotive.com/) — Official API reference

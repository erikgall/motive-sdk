## Motive ELD Laravel SDK

This package provides a fluent Laravel SDK for the Motive ELD (Electronic Logging Device) API.

### Installation

@verbatim
<code-snippet name="Install via Composer" lang="bash">
composer require motive/sdk
</code-snippet>
@endverbatim

### Configuration

Publish the configuration file:

@verbatim
<code-snippet name="Publish config" lang="bash">
php artisan vendor:publish --provider="Motive\MotiveServiceProvider"
</code-snippet>
@endverbatim

Set your API key in `.env`:

@verbatim
<code-snippet name="Environment variables" lang="env">
MOTIVE_API_KEY=your-api-key
MOTIVE_BASE_URL=https://api.gomotive.com
</code-snippet>
@endverbatim

### Quick Start

@verbatim
<code-snippet name="Basic usage" lang="php">
use Motive\Facades\Motive;

// List all vehicles with lazy pagination
foreach (Motive::vehicles()->list() as $vehicle) {
    echo "{$vehicle->number}: {$vehicle->make} {$vehicle->model}\n";
}

// Find a specific vehicle
$vehicle = Motive::vehicles()->find(123);

// Create a new vehicle
$vehicle = Motive::vehicles()->create([
    'number' => 'TRUCK-001',
    'make' => 'Freightliner',
    'model' => 'Cascadia',
    'year' => 2024,
]);
</code-snippet>
@endverbatim

### Features

- **31 API Resources**: Full coverage of the Motive API (150+ endpoints)
- **Type-Safe DTOs**: 50+ Data Transfer Objects with strict typing
- **Lazy Pagination**: Memory-efficient iteration over large datasets
- **OAuth 2.0 & API Key**: Both authentication methods supported
- **Multi-Tenancy**: Support for multiple named connections
- **Webhook Handling**: Signature verification middleware included
- **Testing Support**: Fakes, factories, and assertions for easy testing

### Context Modifiers

@verbatim
<code-snippet name="Context modifiers" lang="php">
// Set timezone for requests
Motive::withTimezone('America/Chicago')
    ->vehicles()
    ->list();

// Use metric units
Motive::withMetricUnits()
    ->vehicles()
    ->currentLocation(123);

// Use a different connection
Motive::connection('tenant-a')
    ->vehicles()
    ->list();

// Dynamic API key
Motive::withApiKey('custom-key')
    ->users()
    ->list();
</code-snippet>
@endverbatim

### Pagination

@verbatim
<code-snippet name="Pagination options" lang="php">
// Lazy pagination (memory efficient, auto-fetches pages)
$vehicles = Motive::vehicles()->list();
foreach ($vehicles as $vehicle) {
    // Processes one item at a time
}

// Manual pagination with metadata
$page = Motive::vehicles()->paginate(page: 1, perPage: 25);
echo "Page {$page->currentPage()} of {$page->lastPage()}";
echo "Total: {$page->total()}";

foreach ($page->items() as $vehicle) {
    // Process page items
}
</code-snippet>
@endverbatim

### Error Handling

@verbatim
<code-snippet name="Exception handling" lang="php">
use Motive\Exceptions\NotFoundException;
use Motive\Exceptions\RateLimitException;
use Motive\Exceptions\ValidationException;

try {
    $vehicle = Motive::vehicles()->find(999);
} catch (NotFoundException $e) {
    // Vehicle not found (404)
} catch (RateLimitException $e) {
    $retryAfter = $e->retryAfter(); // Seconds to wait
} catch (ValidationException $e) {
    $errors = $e->errors(); // Field validation errors
}
</code-snippet>
@endverbatim

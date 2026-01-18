# Installation

## Requirements

Before installing the Motive SDK, ensure your environment meets the following requirements:

- **PHP 8.2** or higher
- **Laravel 11** or higher
- **Composer** for dependency management

## Install via Composer

Install the package using Composer:

```bash
composer require erikgall/motive-sdk
```

## Publish Configuration

Publish the configuration file to customize settings:

```bash
php artisan vendor:publish --tag=motive-config
```

This will create a `config/motive.php` file in your application.

## Service Provider

The package uses Laravel's auto-discovery, so the service provider will be registered automatically. If you've disabled auto-discovery, add the service provider manually to `config/app.php`:

```php
'providers' => [
    // ...
    Motive\MotiveServiceProvider::class,
],
```

## Facade Registration

The `Motive` facade is also auto-registered. For manual registration, add it to your `config/app.php`:

```php
'aliases' => [
    // ...
    'Motive' => Motive\Facades\Motive::class,
],
```

## Verify Installation

Verify the installation by checking the package version:

```bash
composer show erikgall/motive-sdk
```

Or test the facade in Tinker:

```bash
php artisan tinker
>>> Motive\Facades\Motive::class
=> "Motive\Facades\Motive"
```

## Next Steps

- [Configure your API credentials](configuration.md)
- [Follow the quick start guide](quick-start.md)

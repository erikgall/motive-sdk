# Contributing

Thank you for considering contributing to the Motive SDK! This guide will help you get started.

## Code of Conduct

Please be respectful and constructive in all interactions. We're all here to build great software together.

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 11+

### Setup

1. **Fork and clone the repository:**

```bash
git clone https://github.com/your-username/motive-sdk.git
cd motive-sdk
```

2. **Install dependencies:**

```bash
composer install
```

3. **Run tests to ensure everything is working:**

```bash
./vendor/bin/phpunit
```

## Development Workflow

### Test-Driven Development

This project follows **Test-Driven Development (TDD)**:

1. **Write tests first** - Create failing tests that define expected behavior
2. **Write code** - Implement minimum code to make tests pass
3. **Refactor** - Clean up while keeping tests green

### Code Style

We use Laravel Pint for code formatting:

```bash
# Format all files
./vendor/bin/pint

# Format only changed files
./vendor/bin/pint --dirty
```

### Static Analysis

We use PHPStan at level 8:

```bash
./vendor/bin/phpstan analyse
```

### Running Tests

```bash
# Full test suite
./vendor/bin/phpunit

# Specific test file
./vendor/bin/phpunit tests/Unit/Data/VehicleTest.php

# Specific test method
./vendor/bin/phpunit --filter "test_creates_vehicle"
```

## Coding Standards

### PHP Standards

- Use PHP 8.2+ features (enums, typed properties, constructor property promotion)
- Always use strict types and return type declarations
- Use `CarbonImmutable` for all datetime values
- Use backed enums for status and type fields

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Classes | PascalCase | `VehiclesResource` |
| Methods | camelCase | `findByExternalId` |
| Variables | camelCase | `$vehicleId` |
| Constants | SCREAMING_SNAKE | `DEFAULT_TIMEOUT` |

### DTOs

DTOs extend `DataTransferObject`:

```php
/**
 * Example DTO.
 *
 * @author Your Name <your.email@example.com>
 *
 * @property int $id
 * @property string $name
 * @property SomeStatus $status
 */
class Example extends DataTransferObject
{
    protected array $casts = [
        'id' => 'int',
        'status' => SomeStatus::class,
    ];
}
```

### Resources

Resources extend `Resource`:

```php
class ExampleResource extends Resource
{
    use HasCrudOperations;

    protected string $apiVersion = '1';

    protected function basePath(): string
    {
        return 'examples';
    }

    protected function dtoClass(): string
    {
        return Example::class;
    }

    protected function resourceKey(): string
    {
        return 'example';
    }
}
```

### PHPDoc

Include author tags and type hints:

```php
/**
 * Brief description.
 *
 * @author Your Name <your.email@example.com>
 *
 * @property int $id
 */
```

Use array shapes for complex arrays:

```php
/**
 * @param array{id: int, name: string} $data
 * @return array<string, mixed>
 */
```

## Pull Request Process

1. **Create a branch:**

```bash
git checkout -b feature/your-feature-name
```

2. **Make changes** following TDD principles

3. **Ensure tests pass:**

```bash
./vendor/bin/phpunit
```

4. **Run code style fixer:**

```bash
./vendor/bin/pint --dirty
```

5. **Run static analysis:**

```bash
./vendor/bin/phpstan analyse
```

6. **Commit with descriptive message:**

```bash
git commit -m "feat: Add support for new endpoint"
```

7. **Push and create pull request**

### Commit Message Format

We follow conventional commits:

| Prefix | Description |
|--------|-------------|
| `feat:` | New features |
| `fix:` | Bug fixes |
| `docs:` | Documentation |
| `test:` | Test changes |
| `refactor:` | Code refactoring |
| `chore:` | Maintenance |

## Adding New Features

### New Resource

1. Create resource class in `src/Resources/{Category}/`
2. Create required DTOs in `src/Data/`
3. Create required enums in `src/Enums/`
4. Add accessor method to `MotiveManager`
5. Write comprehensive tests
6. Update README.md

### New DTO

1. Create DTO in `src/Data/`
2. Define `$casts` for type casting
3. Define `$defaults` if needed
4. Add `@property` PHPDoc
5. Write tests

### New Enum

1. Create enum in `src/Enums/`
2. Use backed string enums
3. Write tests

## Questions?

Open an issue for discussion. Thank you for contributing!

# Contributing to Motive SDK

Thank you for considering contributing to the Motive ELD Laravel SDK! This document provides guidelines and information for contributors.

## Code of Conduct

Please be respectful and constructive in all interactions. We're all here to build great software together.

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 11+

### Setup

1. Fork and clone the repository:

```bash
git clone https://github.com/your-username/motive-sdk.git
cd motive-sdk
```

2. Install dependencies:

```bash
composer install
```

3. Run tests to ensure everything is working:

```bash
./vendor/bin/phpunit
```

## Development Workflow

### Test-Driven Development

This project follows **Test-Driven Development (TDD)**. When adding features or fixing bugs:

1. **Write tests first** - Create failing tests that define the expected behavior
2. **Write code** - Implement the minimum code to make tests pass
3. **Refactor** - Clean up the code while keeping tests green

### Code Style

We use Laravel Pint for code formatting. Always run Pint before committing:

```bash
./vendor/bin/pint
```

For checking only changed files:

```bash
./vendor/bin/pint --dirty
```

### Static Analysis

We use PHPStan at level 8 for static analysis:

```bash
./vendor/bin/phpstan analyse
```

### Running Tests

Run the full test suite:

```bash
./vendor/bin/phpunit
```

Run specific test files or methods:

```bash
./vendor/bin/phpunit --filter "VehicleTest"
./vendor/bin/phpunit tests/Unit/Data/VehicleTest.php
```

## Coding Standards

### PHP Standards

- Use PHP 8.2+ features (enums, typed properties, constructor property promotion)
- Always use strict types and return type declarations
- Use `CarbonImmutable` for all datetime values
- Use backed enums for status and type fields

### Naming Conventions

- **Classes**: PascalCase (e.g., `VehiclesResource`, `HosLog`)
- **Methods**: camelCase (e.g., `findByExternalId`, `getCurrentLocation`)
- **Variables**: camelCase (e.g., `$vehicleId`, `$hosLogs`)
- **Constants**: SCREAMING_SNAKE_CASE (e.g., `DEFAULT_TIMEOUT`)

### DTOs (Data Transfer Objects)

DTOs extend the `DataTransferObject` base class which uses Laravel Fluent:

```php
/**
 * Example DTO.
 *
 * @author Your Name <your.email@example.com>
 *
 * @property int $id
 * @property string $name
 * @property SomeStatus $status
 * @property CarbonImmutable|null $createdAt
 */
class Example extends DataTransferObject
{
    protected array $casts = [
        'id' => 'int',
        'status' => SomeStatus::class,
        'createdAt' => CarbonImmutable::class,
    ];

    protected array $defaults = [
        'active' => true,
    ];
}
```

### Resources

Resources extend the `Resource` base class:

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

Include author tags and type hints in all class-level PHPDoc:

```php
/**
 * Brief description of the class.
 *
 * @author Your Name <your.email@example.com>
 *
 * @property int $id
 * @property string $name
 */
```

Use array shapes for complex arrays:

```php
/**
 * @param array{id: int, name: string, items: array<int, Item>} $data
 * @return array<string, mixed>
 */
```

## Pull Request Process

1. **Create a branch** from `main`:

```bash
git checkout -b feature/your-feature-name
```

2. **Make your changes** following TDD principles

3. **Ensure all tests pass**:

```bash
./vendor/bin/phpunit
```

4. **Run code style fixer**:

```bash
./vendor/bin/pint --dirty
```

5. **Run static analysis**:

```bash
./vendor/bin/phpstan analyse
```

6. **Commit your changes** with a descriptive message:

```bash
git commit -m "feat: Add support for new endpoint

Detailed description of changes...

Co-Authored-By: Your Name <your.email@example.com>"
```

7. **Push and create a pull request**

### Commit Message Format

We follow conventional commits:

- `feat:` New features
- `fix:` Bug fixes
- `docs:` Documentation changes
- `test:` Test additions or modifications
- `refactor:` Code refactoring
- `chore:` Maintenance tasks

## Adding New Features

### Adding a New Resource

1. Create the resource class in `src/Resources/{Category}/`
2. Create any required DTOs in `src/Data/`
3. Create any required enums in `src/Enums/`
4. Add the resource accessor method to `MotiveManager`
5. Write comprehensive tests in `tests/Unit/` and `tests/Feature/`
6. Update the README.md with usage examples

### Adding a New DTO

1. Create the DTO in `src/Data/`
2. Define `$casts` for type casting
3. Define `$defaults` for default values if needed
4. Add `@property` PHPDoc annotations for IDE support
5. Write tests in `tests/Unit/Data/`

### Adding a New Enum

1. Create the enum in `src/Enums/`
2. Use backed string enums for API compatibility
3. Write tests in `tests/Unit/Enums/`

## Questions?

If you have questions about contributing, please open an issue for discussion.

Thank you for contributing!

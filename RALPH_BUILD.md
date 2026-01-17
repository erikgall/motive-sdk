# Ralph Build Instructions - Motive ELD Laravel SDK

## Project Context

You are building a first-party-quality Laravel SDK for the Motive ELD API following Test-Driven Development (TDD) principles.

**Tech Stack:**
- PHP 8.2+
- Laravel 11+
- PHPUnit for testing
- Laravel Pint for code style
- PHPStan level 8 for static analysis

**Development Flow:**
1. **RED**: Write failing test first
2. **GREEN**: Write minimal code to pass test
3. **REFACTOR**: Clean up code, improve naming, remove duplication

## Code Quality Standards

### Naming Conventions
- **Classes**: PascalCase, descriptive nouns (e.g., `ApiKeyAuthenticator`, `VehiclesResource`)
- **Methods**: camelCase, verb-based (e.g., `authenticate()`, `listVehicles()`)
- **Variables**: camelCase, descriptive (e.g., `$apiKey`, `$vehicleData`)
- **Test Methods**: snake_case with `it_` prefix (e.g., `it_adds_api_key_header_to_request`)

### Code Simplicity
- Single Responsibility Principle - each class does ONE thing
- No code duplication - extract to shared methods/traits
- Prefer composition over inheritance
- Keep methods under 20 lines when possible

### Architecture Principles
- **Services**: Each resource is a service (VehiclesResource, UsersResource)
- **DTOs**: Immutable data transfer objects with typed properties
- **Contracts**: Interfaces for authenticators and token storage
- **Separation**: Clear boundaries between HTTP client, auth, resources, DTOs

## Phase 1: Foundation (Core Infrastructure)

Build in this exact order, writing tests FIRST for each component:

### 1.1 Package Setup

```bash
# Initialize composer.json with proper structure
composer init --name="motive/sdk" --type=library --require="php:^8.2" --require="illuminate/support:^11.0" --require="illuminate/http:^11.0"

# Add dev dependencies
composer require --dev phpunit/phpunit:"^11.0" phpstan/phpstan:"^1.10" laravel/pint:"^1.13"

# Configure autoloading in composer.json
{
    "autoload": {
        "psr-4": {
            "Motive\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Motive\\Tests\\": "tests/"
        }
    }
}
```

Create these config files:

**phpunit.xml**:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         testdox="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

**phpstan.neon**:
```neon
parameters:
    level: 8
    paths:
        - src
        - tests
```

**pint.json**:
```json
{
    "preset": "laravel"
}
```

### 1.2 Build Contracts First (Interfaces)

#### Test: `tests/Unit/Contracts/AuthenticatorContractTest.php`
```php
<?php

namespace Motive\Tests\Unit\Contracts;

use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;
use PHPUnit\Framework\TestCase;

class AuthenticatorContractTest extends TestCase
{
    /** @test */
    public function it_defines_authenticator_interface(): void
    {
        $this->assertTrue(interface_exists(Authenticator::class));
        
        $methods = get_class_methods(Authenticator::class);
        $this->assertContains('authenticate', $methods);
        $this->assertContains('isExpired', $methods);
        $this->assertContains('refresh', $methods);
    }
}
```

#### Implementation: `src/Contracts/Authenticator.php`
```php
<?php

namespace Motive\Contracts;

use Motive\Client\PendingRequest;

interface Authenticator
{
    /**
     * Add authentication to the pending request
     */
    public function authenticate(PendingRequest $request): PendingRequest;
    
    /**
     * Check if authentication credentials are expired
     */
    public function isExpired(): bool;
    
    /**
     * Refresh authentication credentials
     */
    public function refresh(): void;
}
```

#### Test: `tests/Unit/Contracts/TokenStoreContractTest.php`
```php
<?php

namespace Motive\Tests\Unit\Contracts;

use Motive\Contracts\TokenStore;
use PHPUnit\Framework\TestCase;

class TokenStoreContractTest extends TestCase
{
    /** @test */
    public function it_defines_token_store_interface(): void
    {
        $this->assertTrue(interface_exists(TokenStore::class));
        
        $methods = get_class_methods(TokenStore::class);
        $this->assertContains('getAccessToken', $methods);
        $this->assertContains('getRefreshToken', $methods);
        $this->assertContains('getExpiresAt', $methods);
        $this->assertContains('store', $methods);
    }
}
```

#### Implementation: `src/Contracts/TokenStore.php`
```php
<?php

namespace Motive\Contracts;

use Carbon\CarbonInterface;

interface TokenStore
{
    public function getAccessToken(): ?string;
    
    public function getRefreshToken(): ?string;
    
    public function getExpiresAt(): ?CarbonInterface;
    
    public function store(string $accessToken, string $refreshToken, CarbonInterface $expiresAt): void;
}
```

### 1.3 Build Exception Hierarchy (Bottom-Up)

Create base exception first, then specific exceptions.

#### Test: `tests/Unit/Exceptions/MotiveExceptionTest.php`
```php
<?php

namespace Motive\Tests\Unit\Exceptions;

use Motive\Client\Response;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\TestCase;

class MotiveExceptionTest extends TestCase
{
    /** @test */
    public function it_stores_response_object(): void
    {
        $response = $this->createMock(Response::class);
        $exception = new MotiveException('Test error', $response);
        
        $this->assertSame($response, $exception->getResponse());
    }
    
    /** @test */
    public function it_returns_null_when_no_response(): void
    {
        $exception = new MotiveException('Test error');
        
        $this->assertNull($exception->getResponse());
    }
    
    /** @test */
    public function it_returns_response_body_as_array(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn(['error' => 'Not found']);
        
        $exception = new MotiveException('Test error', $response);
        
        $this->assertEquals(['error' => 'Not found'], $exception->getResponseBody());
    }
}
```

#### Implementation: `src/Exceptions/MotiveException.php`
```php
<?php

namespace Motive\Exceptions;

use Exception;
use Motive\Client\Response;

class MotiveException extends Exception
{
    public function __construct(
        string $message,
        protected ?Response $response = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    
    public function getResponse(): ?Response
    {
        return $this->response;
    }
    
    public function getResponseBody(): ?array
    {
        return $this->response?->json();
    }
}
```

Now create specific exception tests and implementations:

#### `tests/Unit/Exceptions/AuthenticationExceptionTest.php`
#### `src/Exceptions/AuthenticationException.php` (extends MotiveException)

#### `tests/Unit/Exceptions/ValidationExceptionTest.php`
```php
<?php

namespace Motive\Tests\Unit\Exceptions;

use Motive\Client\Response;
use Motive\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    /** @test */
    public function it_extracts_validation_errors_from_response(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn([
            'errors' => [
                'number' => ['The number field is required.'],
                'make' => ['The make field must be a string.']
            ]
        ]);
        
        $exception = new ValidationException('Validation failed', $response);
        
        $this->assertEquals([
            'number' => ['The number field is required.'],
            'make' => ['The make field must be a string.']
        ], $exception->errors());
    }
}
```

#### `src/Exceptions/ValidationException.php`
```php
<?php

namespace Motive\Exceptions;

class ValidationException extends MotiveException
{
    public function errors(): array
    {
        return $this->response?->json('errors') ?? [];
    }
}
```

Create similar patterns for:
- `AuthorizationException` (403)
- `NotFoundException` (404)
- `RateLimitException` (429) - with `retryAfter()` method
- `ServerException` (5xx)
- `WebhookVerificationException`

### 1.4 Build Client Layer (PendingRequest and Response)

#### Test: `tests/Unit/Client/ResponseTest.php`
```php
<?php

namespace Motive\Tests\Unit\Client;

use Illuminate\Http\Client\Response as LaravelResponse;
use Motive\Client\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /** @test */
    public function it_wraps_laravel_response(): void
    {
        $laravelResponse = new LaravelResponse(
            new \GuzzleHttp\Psr7\Response(200, [], json_encode(['data' => 'test']))
        );
        
        $response = new Response($laravelResponse);
        
        $this->assertEquals(200, $response->status());
        $this->assertTrue($response->successful());
        $this->assertEquals(['data' => 'test'], $response->json());
    }
    
    /** @test */
    public function it_can_get_nested_json_keys(): void
    {
        $laravelResponse = new LaravelResponse(
            new \GuzzleHttp\Psr7\Response(200, [], json_encode([
                'vehicle' => ['id' => 123, 'number' => 'TRUCK-001']
            ]))
        );
        
        $response = new Response($laravelResponse);
        
        $this->assertEquals(['id' => 123, 'number' => 'TRUCK-001'], $response->json('vehicle'));
        $this->assertEquals('TRUCK-001', $response->json('vehicle.number'));
    }
}
```

#### Implementation: `src/Client/Response.php`
```php
<?php

namespace Motive\Client;

use Illuminate\Http\Client\Response as LaravelResponse;

class Response
{
    public function __construct(
        protected LaravelResponse $response
    ) {}
    
    public function status(): int
    {
        return $this->response->status();
    }
    
    public function successful(): bool
    {
        return $this->response->successful();
    }
    
    public function json(?string $key = null): mixed
    {
        return $this->response->json($key);
    }
    
    public function body(): string
    {
        return $this->response->body();
    }
    
    public function header(string $header): ?string
    {
        return $this->response->header($header);
    }
}
```

#### Test: `tests/Unit/Client/PendingRequestTest.php`
```php
<?php

namespace Motive\Tests\Unit\Client;

use Illuminate\Http\Client\PendingRequest as LaravelPendingRequest;
use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;
use PHPUnit\Framework\TestCase;

class PendingRequestTest extends TestCase
{
    /** @test */
    public function it_adds_headers_fluently(): void
    {
        $config = ['base_url' => 'https://api.gomotive.com'];
        $authenticator = $this->createMock(Authenticator::class);
        
        $request = new PendingRequest($config, $authenticator);
        $result = $request->withHeader('X-Custom', 'value');
        
        $this->assertInstanceOf(PendingRequest::class, $result);
        $this->assertNotSame($request, $result); // Immutable
    }
    
    /** @test */
    public function it_applies_authentication(): void
    {
        $config = ['base_url' => 'https://api.gomotive.com'];
        $authenticator = $this->createMock(Authenticator::class);
        
        $request = new PendingRequest($config, $authenticator);
        
        $authenticator->expects($this->once())
            ->method('authenticate')
            ->with($request)
            ->willReturn($request);
        
        $request->withAuthentication();
    }
}
```

### 1.5 Build Authentication

#### Test: `tests/Unit/Auth/ApiKeyAuthenticatorTest.php`
```php
<?php

namespace Motive\Tests\Unit\Auth;

use Motive\Auth\ApiKeyAuthenticator;
use Motive\Client\PendingRequest;
use PHPUnit\Framework\TestCase;

class ApiKeyAuthenticatorTest extends TestCase
{
    /** @test */
    public function it_adds_api_key_header_to_request(): void
    {
        $apiKey = 'test-api-key-123';
        $authenticator = new ApiKeyAuthenticator($apiKey);
        
        $request = $this->createMock(PendingRequest::class);
        $request->expects($this->once())
            ->method('withHeader')
            ->with('X-Api-Key', $apiKey)
            ->willReturnSelf();
        
        $result = $authenticator->authenticate($request);
        
        $this->assertSame($request, $result);
    }
    
    /** @test */
    public function it_never_expires(): void
    {
        $authenticator = new ApiKeyAuthenticator('key');
        
        $this->assertFalse($authenticator->isExpired());
    }
    
    /** @test */
    public function refresh_does_nothing_for_api_keys(): void
    {
        $authenticator = new ApiKeyAuthenticator('key');
        
        // Should not throw
        $authenticator->refresh();
        $this->assertTrue(true);
    }
}
```

#### Implementation: `src/Auth/ApiKeyAuthenticator.php`
```php
<?php

namespace Motive\Auth;

use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;

class ApiKeyAuthenticator implements Authenticator
{
    public function __construct(
        private readonly string $apiKey
    ) {}
    
    public function authenticate(PendingRequest $request): PendingRequest
    {
        return $request->withHeader('X-Api-Key', $this->apiKey);
    }
    
    public function isExpired(): bool
    {
        return false;
    }
    
    public function refresh(): void
    {
        // API keys don't expire or refresh
    }
}
```

### 1.6 Build Base Resource and Traits

#### Test: `tests/Unit/Resources/ResourceTest.php`
```php
<?php

namespace Motive\Tests\Unit\Resources;

use Motive\Client\MotiveClient;
use Motive\Resources\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    /** @test */
    public function it_constructs_full_path_with_api_version(): void
    {
        $client = $this->createMock(MotiveClient::class);
        
        $resource = new class($client) extends Resource {
            protected string $apiVersion = '1';
            
            protected function basePath(): string
            {
                return 'vehicles';
            }
            
            protected function resourceKey(): string
            {
                return 'vehicle';
            }
            
            protected function dtoClass(): string
            {
                return \stdClass::class;
            }
        };
        
        $this->assertEquals('/v1/vehicles', $resource->fullPath());
    }
}
```

#### Implementation: `src/Resources/Resource.php`
```php
<?php

namespace Motive\Resources;

use Motive\Client\MotiveClient;

abstract class Resource
{
    protected string $apiVersion;
    
    public function __construct(
        protected MotiveClient $client
    ) {}
    
    abstract protected function basePath(): string;
    abstract protected function resourceKey(): string;
    abstract protected function dtoClass(): string;
    
    public function fullPath(?string $suffix = null): string
    {
        $path = "/v{$this->apiVersion}/{$this->basePath()}";
        
        return $suffix ? "{$path}/{$suffix}" : $path;
    }
}
```

### 1.7 Build DTOs

#### Test: `tests/Unit/Data/DataTransferObjectTest.php`
```php
<?php

namespace Motive\Tests\Unit\Data;

use Carbon\CarbonImmutable;
use Motive\Data\DataTransferObject;
use PHPUnit\Framework\TestCase;

class DataTransferObjectTest extends TestCase
{
    /** @test */
    public function it_hydrates_from_array(): void
    {
        $dto = TestDto::from([
            'id' => 123,
            'name' => 'Test',
            'created_at' => '2024-01-17T10:00:00Z'
        ]);
        
        $this->assertEquals(123, $dto->id);
        $this->assertEquals('Test', $dto->name);
        $this->assertInstanceOf(CarbonImmutable::class, $dto->createdAt);
    }
    
    /** @test */
    public function it_converts_to_array(): void
    {
        $dto = new TestDto(
            id: 123,
            name: 'Test',
            createdAt: CarbonImmutable::parse('2024-01-17T10:00:00Z')
        );
        
        $array = $dto->toArray();
        
        $this->assertEquals(123, $array['id']);
        $this->assertEquals('Test', $array['name']);
        $this->assertIsString($array['created_at']);
    }
}

class TestDto extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $name,
        public CarbonImmutable $createdAt
    ) {}
}
```

### 1.8 Build Pagination

Create tests and implementations for:
- `Paginator` - Core pagination logic
- `PaginatedResponse` - Metadata wrapper
- `LazyPaginator` - Generator-based iteration

### 1.9 Build Configuration

#### `config/motive.php`
```php
<?php

return [
    'default' => env('MOTIVE_CONNECTION', 'default'),
    
    'connections' => [
        'default' => [
            'auth_driver' => env('MOTIVE_AUTH_DRIVER', 'api_key'),
            'api_key' => env('MOTIVE_API_KEY'),
            'oauth' => [
                'client_id' => env('MOTIVE_CLIENT_ID'),
                'client_secret' => env('MOTIVE_CLIENT_SECRET'),
                'redirect_uri' => env('MOTIVE_REDIRECT_URI'),
            ],
            'base_url' => env('MOTIVE_BASE_URL', 'https://api.gomotive.com'),
            'timeout' => (int) env('MOTIVE_TIMEOUT', 30),
            'retry' => [
                'times' => (int) env('MOTIVE_RETRY_TIMES', 3),
                'sleep' => (int) env('MOTIVE_RETRY_SLEEP', 100),
            ],
        ],
    ],
    
    'headers' => [
        'timezone' => env('MOTIVE_TIMEZONE'),
        'metric_units' => (bool) env('MOTIVE_METRIC_UNITS', false),
    ],
    
    'webhooks' => [
        'secret' => env('MOTIVE_WEBHOOK_SECRET'),
        'tolerance' => (int) env('MOTIVE_WEBHOOK_TOLERANCE', 300),
    ],
];
```

### 1.10 Build Manager and Service Provider

These tie everything together.

#### Test: `tests/Unit/MotiveManagerTest.php` 
Test connection management, context modifiers, resource accessors.

#### Implementation: `src/MotiveManager.php`
Manager with fluent API for connections and resources.

#### `src/MotiveServiceProvider.php`
Laravel service provider for registration.

#### `src/Facades/Motive.php`
Facade for clean API access.

## Phase 2: Essential Resources

Build in this order (always test-first):

### 2.1 Vehicles Resource
- Test CRUD operations
- Test `findByNumber()` custom method
- Test `currentLocation()` and `locations()` methods
- Create `VehiclesResource`, `Vehicle` DTO, `VehicleLocation` DTO, `VehicleStatus` enum

### 2.2 Users Resource
- Test CRUD operations
- Test `deactivate()` and `reactivate()` methods
- Create `UsersResource`, `User` DTO, `Driver` DTO, enums

### 2.3 Assets Resource
- Test CRUD operations
- Test vehicle assignment methods
- Create resource, DTOs, enums

### 2.4 Companies Resource
- Test `current()` method
- Create resource and DTO

## Phase 3-11: Continue Building

Follow the same pattern for all remaining phases:
1. Write test FIRST (RED)
2. Write minimal implementation (GREEN)
3. Refactor for clarity and simplicity
4. Run `./vendor/bin/pint` after each class
5. Run `./vendor/bin/phpstan analyse` periodically

## Testing Strategy

### Unit Tests
- Test each service/class in isolation
- Mock dependencies
- Focus on business logic

### Feature Tests
- Test integration between services
- Use real HTTP client (with mocked responses)
- Test full workflows

### Running Tests
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific suite
./vendor/bin/phpunit --testsuite=Unit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage

# Run single test
./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php
```

## Code Quality Checks

After each phase:
```bash
# Fix code style
./vendor/bin/pint

# Run static analysis
./vendor/bin/phpstan analyse

# Run full test suite
./vendor/bin/phpunit
```

## Ralph Execution Instructions

Ralph, follow these steps:

1. **Initialize the project** - Run composer init, install dependencies, create config files
2. **Build Phase 1 completely** - Create all tests FIRST, then implementations
3. **Verify Phase 1** - Run phpunit, pint, phpstan
4. **Build Phase 2 completely** - Same TDD approach
5. **Continue through all phases** - Always test-first

For each class you build:
1. Create the test file first with clear test cases
2. Run phpunit - verify it FAILS (RED)
3. Create minimal implementation to pass test (GREEN)
4. Refactor if needed for clarity
5. Run pint to fix style
6. Move to next class

**Output Format:**
- Show me each test file you create
- Show me each implementation file you create
- Show me the test results (passed/failed)
- Alert me to any issues

**Priority Order:**
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5 → Continue through Phase 11

Ready to build this SDK properly with TDD!

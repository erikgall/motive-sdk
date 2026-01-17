# Ralph Quick Reference - TDD Workflow

## The TDD Cycle (Repeat for Every Class)

### 1️⃣ RED Phase (Write Failing Test)

```bash
# Create test file
touch tests/Unit/Auth/ApiKeyAuthenticatorTest.php

# Write test that describes desired behavior
# Run: ./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php
# ❌ Should FAIL - this confirms we're testing the right thing
```

### 2️⃣ GREEN Phase (Make It Pass)

```bash
# Create implementation file
touch src/Auth/ApiKeyAuthenticator.php

# Write MINIMAL code to pass the test
# Run: ./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php
# ✅ Should PASS
```

### 3️⃣ REFACTOR Phase (Clean Up)

```bash
# Improve code quality:
# - Better naming?
# - Remove duplication?
# - Simplify logic?
# - Add type hints?

# Run: ./vendor/bin/pint  # Fix code style
# Run: ./vendor/bin/phpunit  # Ensure tests still pass
```

## Build Order (Follow Strictly)

### Phase 1: Foundation
1. ✅ Contracts (interfaces first)
2. ✅ Exceptions (base to specific)
3. ✅ Client (Response → PendingRequest → MotiveClient)
4. ✅ Auth (ApiKeyAuthenticator)
5. ✅ DTOs (base DataTransferObject)
6. ✅ Pagination (Paginator → PaginatedResponse → LazyPaginator)
7. ✅ Resources (base Resource + traits)
8. ✅ Manager (MotiveManager)
9. ✅ Service Provider
10. ✅ Facade

### Phase 2: Essential Resources
1. ✅ VehiclesResource + Vehicle DTO + VehicleStatus enum
2. ✅ UsersResource + User DTO + Driver DTO + enums
3. ✅ AssetsResource + Asset DTO + enums
4. ✅ CompaniesResource + Company DTO

Continue with Phase 3-11 as documented in RALPH_BUILD.md

## Code Quality Checklist (After Each Class)

```bash
# ✅ Tests pass
./vendor/bin/phpunit

# ✅ Code style is correct
./vendor/bin/pint

# ✅ Static analysis passes
./vendor/bin/phpstan analyse

# ✅ All methods have type hints
# ✅ All properties have types
# ✅ Naming is clear and descriptive
# ✅ No code duplication
# ✅ Single responsibility per class
```

## Test Naming Convention

```php
// ✅ Good test names (describe behavior)
public function it_adds_api_key_header_to_request(): void
public function it_never_expires(): void
public function it_extracts_validation_errors_from_response(): void
public function it_constructs_full_path_with_api_version(): void

// ❌ Bad test names (not descriptive)
public function test_authenticate(): void
public function test_error(): void
```

## Class Naming Convention

```php
// Services (Resources)
VehiclesResource
UsersResource
HosLogsResource

// DTOs (Data Transfer Objects)
Vehicle
User
HosLog
VehicleLocation

// Authenticators
ApiKeyAuthenticator
OAuthAuthenticator

// Exceptions
MotiveException (base)
AuthenticationException
ValidationException

// Enums
VehicleStatus
DutyStatus
UserRole
```

## Method Naming Convention

```php
// ✅ Good method names (verb-based, clear intent)
public function authenticate(PendingRequest $request): PendingRequest
public function listVehicles(array $params = []): LazyCollection
public function findByNumber(string $number): Vehicle
public function isExpired(): bool

// ❌ Bad method names (vague, not descriptive)
public function process()
public function handle()
public function do()
```

## Architecture Pattern

Every resource follows this pattern:

```
tests/Unit/Resources/Vehicles/VehiclesResourceTest.php  ← Write this FIRST
    ↓ RED (fails)
src/Resources/Vehicles/VehiclesResource.php              ← Write this SECOND
    ↓ GREEN (passes)
Refactor both for clarity
    ↓
tests/Unit/Data/VehicleTest.php                          ← Write this FIRST
    ↓ RED
src/Data/Vehicle.php                                     ← Write this SECOND
    ↓ GREEN
Refactor
    ↓
tests/Unit/Enums/VehicleStatusTest.php                   ← Write this FIRST
    ↓ RED
src/Enums/VehicleStatus.php                              ← Write this SECOND
    ↓ GREEN
Done with VehiclesResource!
```

## Common Test Patterns

### Testing a Service/Resource

```php
public function it_performs_crud_operations(): void
{
    // Arrange
    $client = $this->createMock(MotiveClient::class);
    $resource = new VehiclesResource($client);
    
    // Act
    $result = $resource->list();
    
    // Assert
    $this->assertInstanceOf(LazyCollection::class, $result);
}
```

### Testing DTOs

```php
public function it_hydrates_from_array(): void
{
    // Arrange
    $data = ['id' => 123, 'number' => 'TRUCK-001'];
    
    // Act
    $vehicle = Vehicle::from($data);
    
    // Assert
    $this->assertEquals(123, $vehicle->id);
    $this->assertEquals('TRUCK-001', $vehicle->number);
}
```

### Testing Enums

```php
public function it_has_all_expected_cases(): void
{
    $cases = VehicleStatus::cases();
    
    $this->assertContains(VehicleStatus::Active, $cases);
    $this->assertContains(VehicleStatus::Inactive, $cases);
}
```

## Running Tests During Development

```bash
# Run all tests
./vendor/bin/phpunit

# Run only unit tests
./vendor/bin/phpunit --testsuite=Unit

# Run specific test file
./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php

# Run with testdox (readable output)
./vendor/bin/phpunit --testdox

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

## Common Issues & Solutions

### Issue: Test can't find class
```bash
# Solution: Run composer dump-autoload
composer dump-autoload
```

### Issue: Style errors
```bash
# Solution: Run pint
./vendor/bin/pint
```

### Issue: Type errors
```bash
# Solution: Add type hints to all methods and properties
public function list(array $params = []): LazyCollection  # ✅
public function list($params = [])  # ❌
```

## Questions to Ask Before Moving On

After each class, ask:

1. ✅ Do all tests pass?
2. ✅ Is the naming clear and descriptive?
3. ✅ Is the code simple (no unnecessary complexity)?
4. ✅ Are there any duplications to extract?
5. ✅ Does it follow single responsibility?
6. ✅ Are all types declared?
7. ✅ Does it match Laravel conventions?

If YES to all → Move to next class
If NO to any → Refactor until YES

## Ralph's Checklist Per Class

- [ ] Create test file
- [ ] Write test cases
- [ ] Run test (verify RED ❌)
- [ ] Create implementation file
- [ ] Write minimal code
- [ ] Run test (verify GREEN ✅)
- [ ] Refactor for quality
- [ ] Run pint
- [ ] Verify tests still pass
- [ ] Move to next class

Good luck building! Remember: **Test First, Always** 🧪

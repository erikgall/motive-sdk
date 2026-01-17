# START HERE - Ralph Build Instructions

## Step 1: Initialize the Project

> Step completed manually

## Step 2: Verify Setup

```bash
# Should show PHPUnit is installed
./vendor/bin/phpunit --version

# Should show Pint is installed
./vendor/bin/pint --version

# Should show PHPStan is installed
./vendor/bin/phpstan --version
```

## Step 3: Read the Documentation

Read these files IN ORDER:

1. **RALPH_QUICK_REFERENCE.md** ← Quick cheat sheet for TDD workflow
2. **RALPH_BUILD.md** ← Comprehensive build instructions with examples
3. **PLAN.md** ← High-level architecture and design decisions
4. **TODO.md** ← Complete task breakdown with checkboxes

## Step 4: Start Building (Phase 1)

Follow the TDD cycle for EVERY class:

### Example: Building ApiKeyAuthenticator

```bash
# 1. Create test FIRST (RED Phase)
cat > tests/Unit/Auth/ApiKeyAuthenticatorTest.php << 'EOF'
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
}
EOF

# 2. Run test - should FAIL (RED ❌)
./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php

# 3. Create implementation (GREEN Phase)
cat > src/Auth/ApiKeyAuthenticator.php << 'EOF'
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
        // API keys don't expire
    }
}
EOF

# 4. Run test - should PASS (GREEN ✅)
./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php

# 5. Fix code style (REFACTOR Phase)
./vendor/bin/pint

# 6. Verify still passes
./vendor/bin/phpunit tests/Unit/Auth/ApiKeyAuthenticatorTest.php

# ✅ Done! Move to next class
```

## Step 5: Build Order (Strict Sequence)

Build these in EXACT order (see RALPH_BUILD.md for details):

### Phase 1: Foundation
1. Contracts (Authenticator, TokenStore)
2. Exceptions (MotiveException → specific exceptions)
3. Client (Response → PendingRequest → MotiveClient)
4. Auth (ApiKeyAuthenticator)
5. DTOs (DataTransferObject base)
6. Pagination (all 3 classes)
7. Resources (base Resource + traits)
8. Config file
9. Manager (MotiveManager)
10. Service Provider
11. Facade

### Phase 2: Essential Resources
1. VehiclesResource (+ DTOs + enums)
2. UsersResource (+ DTOs + enums)
3. AssetsResource (+ DTOs + enums)
4. CompaniesResource (+ DTO)

Continue with Phases 3-11 as documented.

## Step 6: Quality Checks (After Each Class)

```bash
# ✅ Run tests
./vendor/bin/phpunit

# ✅ Fix style
./vendor/bin/pint

# ✅ Type checking (run periodically)
./vendor/bin/phpstan analyse
```

## Step 7: Track Progress

Update TODO.md as you complete items:

```markdown
- [x] **1.4.1** Create `src/Contracts/Authenticator.php`
- [x] **1.5.1** Create `src/Auth/ApiKeyAuthenticator.php`
- [ ] **1.6.1** Create `src/Exceptions/MotiveException.php`
```

## Key Principles to Remember

1. **ALWAYS write test FIRST** - This is non-negotiable
2. **Keep it simple** - Minimal code to pass tests
3. **Refactor fearlessly** - Tests give you confidence
4. **Name things clearly** - Code should read like English
5. **One responsibility per class** - Services do ONE thing well
6. **Type everything** - PHP 8.2 strict types
7. **Run pint frequently** - Keep code style consistent

## Common Mistakes to Avoid

❌ **Writing implementation before test**
✅ Always test first (RED → GREEN → REFACTOR)

❌ **Over-engineering** 
✅ Write minimal code to pass the test

❌ **Skipping refactor phase**
✅ Always clean up after tests pass

❌ **Generic names** like `handle()`, `process()`
✅ Descriptive names like `authenticate()`, `listVehicles()`

❌ **Missing type hints**
✅ Every method and property typed

## Need Help?

If stuck, refer to:
- **RALPH_QUICK_REFERENCE.md** - Quick patterns and examples
- **RALPH_BUILD.md** - Detailed instructions with code examples
- **PLAN.md** - Architecture decisions and design philosophy

## Ready to Build?

```bash
# Initialize project - ALEADY COMPLETE

# Start Phase 1
# Follow TDD cycle for each class
# Test first, always!

# You got this! 🚀
```

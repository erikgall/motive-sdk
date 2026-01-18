# Token Storage

When using OAuth authentication, you need to store access tokens securely. The SDK provides a `TokenStore` contract for custom token storage implementations.

## TokenStore Contract

The `TokenStore` contract defines the interface for token storage:

```php
namespace Motive\Contracts;

use Carbon\CarbonInterface;

interface TokenStore
{
    public function getAccessToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getExpiresAt(): ?CarbonInterface;

    public function store(
        string $accessToken,
        string $refreshToken,
        CarbonInterface $expiresAt
    ): void;
}
```

## Database Token Store

The most common approach is storing tokens in the database:

```php
<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Motive\Contracts\TokenStore;

class DatabaseTokenStore implements TokenStore
{
    public function __construct(
        protected User $user
    ) {}

    public function getAccessToken(): ?string
    {
        return $this->user->motive_access_token;
    }

    public function getRefreshToken(): ?string
    {
        return $this->user->motive_refresh_token;
    }

    public function getExpiresAt(): ?CarbonInterface
    {
        return $this->user->motive_token_expires_at;
    }

    public function store(
        string $accessToken,
        string $refreshToken,
        CarbonInterface $expiresAt
    ): void {
        $this->user->update([
            'motive_access_token' => $accessToken,
            'motive_refresh_token' => $refreshToken,
            'motive_token_expires_at' => $expiresAt,
        ]);
    }
}
```

### Usage

```php
use App\Services\DatabaseTokenStore;
use Motive\Facades\Motive;

$tokenStore = new DatabaseTokenStore($user);

$vehicles = Motive::withTokenStore($tokenStore)
    ->vehicles()
    ->list();
```

## Encrypted Token Store

For additional security, encrypt tokens at rest:

```php
<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Crypt;
use Motive\Contracts\TokenStore;

class EncryptedTokenStore implements TokenStore
{
    public function __construct(
        protected User $user
    ) {}

    public function getAccessToken(): ?string
    {
        if (! $this->user->motive_access_token) {
            return null;
        }

        return Crypt::decryptString($this->user->motive_access_token);
    }

    public function getRefreshToken(): ?string
    {
        if (! $this->user->motive_refresh_token) {
            return null;
        }

        return Crypt::decryptString($this->user->motive_refresh_token);
    }

    public function getExpiresAt(): ?CarbonInterface
    {
        return $this->user->motive_token_expires_at;
    }

    public function store(
        string $accessToken,
        string $refreshToken,
        CarbonInterface $expiresAt
    ): void {
        $this->user->update([
            'motive_access_token' => Crypt::encryptString($accessToken),
            'motive_refresh_token' => Crypt::encryptString($refreshToken),
            'motive_token_expires_at' => $expiresAt,
        ]);
    }
}
```

## Cache Token Store

For high-performance scenarios, use cache storage:

```php
<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Motive\Contracts\TokenStore;

class CacheTokenStore implements TokenStore
{
    public function __construct(
        protected string $userId
    ) {}

    protected function cacheKey(string $type): string
    {
        return "motive_token:{$this->userId}:{$type}";
    }

    public function getAccessToken(): ?string
    {
        return Cache::get($this->cacheKey('access'));
    }

    public function getRefreshToken(): ?string
    {
        return Cache::get($this->cacheKey('refresh'));
    }

    public function getExpiresAt(): ?CarbonInterface
    {
        $timestamp = Cache::get($this->cacheKey('expires_at'));

        return $timestamp ? CarbonImmutable::parse($timestamp) : null;
    }

    public function store(
        string $accessToken,
        string $refreshToken,
        CarbonInterface $expiresAt
    ): void {
        // Store with TTL slightly longer than token expiry
        $ttl = $expiresAt->addDay();

        Cache::put($this->cacheKey('access'), $accessToken, $ttl);
        Cache::put($this->cacheKey('refresh'), $refreshToken, $ttl);
        Cache::put($this->cacheKey('expires_at'), $expiresAt->toIso8601String(), $ttl);
    }
}
```

## Session Token Store

For single-session applications:

```php
<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Carbon\CarbonImmutable;
use Motive\Contracts\TokenStore;

class SessionTokenStore implements TokenStore
{
    public function getAccessToken(): ?string
    {
        return session('motive_access_token');
    }

    public function getRefreshToken(): ?string
    {
        return session('motive_refresh_token');
    }

    public function getExpiresAt(): ?CarbonInterface
    {
        $timestamp = session('motive_token_expires_at');

        return $timestamp ? CarbonImmutable::parse($timestamp) : null;
    }

    public function store(
        string $accessToken,
        string $refreshToken,
        CarbonInterface $expiresAt
    ): void {
        session([
            'motive_access_token' => $accessToken,
            'motive_refresh_token' => $refreshToken,
            'motive_token_expires_at' => $expiresAt->toIso8601String(),
        ]);
    }
}
```

## Automatic Token Refresh

Create a token store that automatically refreshes expired tokens:

```php
<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Motive\Contracts\TokenStore;
use Motive\Facades\Motive;

class AutoRefreshTokenStore implements TokenStore
{
    protected ?string $accessToken = null;
    protected ?string $refreshToken = null;
    protected ?CarbonInterface $expiresAt = null;

    public function __construct(
        protected User $user
    ) {
        $this->accessToken = $user->motive_access_token;
        $this->refreshToken = $user->motive_refresh_token;
        $this->expiresAt = $user->motive_token_expires_at;
    }

    public function getAccessToken(): ?string
    {
        $this->refreshIfNeeded();

        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresAt(): ?CarbonInterface
    {
        return $this->expiresAt;
    }

    public function store(
        string $accessToken,
        string $refreshToken,
        CarbonInterface $expiresAt
    ): void {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;

        $this->user->update([
            'motive_access_token' => $accessToken,
            'motive_refresh_token' => $refreshToken,
            'motive_token_expires_at' => $expiresAt,
        ]);
    }

    protected function refreshIfNeeded(): void
    {
        if (! $this->expiresAt || ! $this->refreshToken) {
            return;
        }

        // Refresh if expiring within 5 minutes
        if ($this->expiresAt->diffInMinutes(now()) > 5) {
            return;
        }

        $newTokens = Motive::oauth()->refreshToken($this->refreshToken);

        $this->store(
            $newTokens->accessToken,
            $newTokens->refreshToken,
            $newTokens->expiresAt
        );
    }
}
```

## Using Token Stores

### With withTokenStore()

```php
$tokenStore = new DatabaseTokenStore($user);

$vehicles = Motive::withTokenStore($tokenStore)
    ->vehicles()
    ->list();
```

### In a Service Provider

Register a default token store:

```php
// AppServiceProvider.php
use App\Services\DatabaseTokenStore;
use Motive\Contracts\TokenStore;

public function register(): void
{
    $this->app->bind(TokenStore::class, function ($app) {
        return new DatabaseTokenStore(auth()->user());
    });
}
```

## Security Considerations

1. **Encrypt sensitive data**: Always encrypt tokens when storing in databases
2. **Use secure session configuration**: If using session storage, ensure sessions are secure
3. **Implement token rotation**: Refresh tokens should be rotated on each use
4. **Set appropriate TTLs**: Don't store tokens longer than necessary
5. **Handle revocation**: Provide a way to clear tokens when users disconnect

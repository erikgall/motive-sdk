# OAuth Authentication

OAuth authentication allows your application to access Motive on behalf of users. This is ideal for multi-tenant SaaS applications where users authorize access to their own fleets.

## Overview

The Motive API uses OAuth 2.0 with the Authorization Code flow:

1. Your app redirects users to Motive's authorization page
2. Users grant access to their fleet
3. Motive redirects back with an authorization code
4. Your app exchanges the code for access tokens
5. Access tokens are used for API requests

## Configuration

### Environment Variables

```env
MOTIVE_AUTH_DRIVER=oauth
MOTIVE_CLIENT_ID=your-client-id
MOTIVE_CLIENT_SECRET=your-client-secret
MOTIVE_REDIRECT_URI=https://your-app.com/motive/callback
```

### Configuration File

```php
// config/motive.php
return [
    'connections' => [
        'default' => [
            'auth_driver' => 'oauth',
            'oauth' => [
                'client_id' => env('MOTIVE_CLIENT_ID'),
                'client_secret' => env('MOTIVE_CLIENT_SECRET'),
                'redirect_uri' => env('MOTIVE_REDIRECT_URI'),
            ],
        ],
    ],
];
```

## OAuth Flow

### Step 1: Generate Authorization URL

Redirect users to Motive's authorization page:

```php
use Motive\Facades\Motive;
use Motive\Enums\Scope;

class MotiveAuthController extends Controller
{
    public function redirect()
    {
        $state = Str::random(40);
        session(['motive_oauth_state' => $state]);

        $url = Motive::oauth()->authorizationUrl(
            scopes: [
                Scope::VehiclesRead,
                Scope::UsersRead,
                Scope::HosRead,
                Scope::DispatchesRead,
            ],
            state: $state,
        );

        return redirect($url);
    }
}
```

### Step 2: Handle Callback

Process the OAuth callback and exchange the code for tokens:

```php
use Motive\Facades\Motive;

class MotiveAuthController extends Controller
{
    public function callback(Request $request)
    {
        // Verify state to prevent CSRF
        if ($request->state !== session('motive_oauth_state')) {
            abort(403, 'Invalid state parameter');
        }

        // Exchange authorization code for tokens
        $tokens = Motive::oauth()->exchangeCode($request->code);

        // Store tokens securely
        $request->user()->update([
            'motive_access_token' => $tokens->accessToken,
            'motive_refresh_token' => $tokens->refreshToken,
            'motive_token_expires_at' => $tokens->expiresAt,
        ]);

        return redirect('/dashboard')
            ->with('success', 'Motive account connected!');
    }
}
```

### Step 3: Use Tokens

Make API requests with the stored tokens:

```php
$user = auth()->user();

$vehicles = Motive::withOAuth(
    accessToken: $user->motive_access_token,
    refreshToken: $user->motive_refresh_token,
    expiresAt: $user->motive_token_expires_at,
)->vehicles()->list();
```

## Token Refresh

Access tokens expire. Refresh them before they expire:

```php
// Check if token is expired or expiring soon
if ($user->motive_token_expires_at->isPast() ||
    $user->motive_token_expires_at->diffInMinutes(now()) < 5
) {
    $newTokens = Motive::oauth()->refreshToken(
        $user->motive_refresh_token
    );

    $user->update([
        'motive_access_token' => $newTokens->accessToken,
        'motive_refresh_token' => $newTokens->refreshToken,
        'motive_token_expires_at' => $newTokens->expiresAt,
    ]);
}
```

## Scopes

Request only the scopes your application needs:

```php
use Motive\Enums\Scope;

$url = Motive::oauth()->authorizationUrl(
    scopes: [
        Scope::VehiclesRead,      // Read vehicle data
        Scope::VehiclesWrite,     // Create/update vehicles
        Scope::UsersRead,         // Read user data
        Scope::HosRead,           // Read HOS data
        Scope::HosWrite,          // Create/edit HOS logs
        Scope::DispatchesRead,    // Read dispatches
        Scope::DispatchesWrite,   // Create/update dispatches
    ],
);
```

See [Scope Enums](../enums/scope-enums.md) for all available scopes.

## Routes

Set up the required routes:

```php
// routes/web.php
use App\Http\Controllers\MotiveAuthController;

Route::get('/motive/connect', [MotiveAuthController::class, 'redirect'])
    ->name('motive.connect');

Route::get('/motive/callback', [MotiveAuthController::class, 'callback'])
    ->name('motive.callback');
```

Configure the callback URL in your Motive application settings to match.

## Database Migration

Add columns to store tokens:

```php
Schema::table('users', function (Blueprint $table) {
    $table->text('motive_access_token')->nullable();
    $table->text('motive_refresh_token')->nullable();
    $table->timestamp('motive_token_expires_at')->nullable();
});
```

## Complete Controller Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Motive\Facades\Motive;
use Motive\Enums\Scope;

class MotiveAuthController extends Controller
{
    public function redirect()
    {
        $state = Str::random(40);
        session(['motive_oauth_state' => $state]);

        $url = Motive::oauth()->authorizationUrl(
            scopes: [
                Scope::VehiclesRead,
                Scope::UsersRead,
                Scope::HosRead,
            ],
            state: $state,
        );

        return redirect($url);
    }

    public function callback(Request $request)
    {
        if ($request->state !== session('motive_oauth_state')) {
            abort(403, 'Invalid state parameter');
        }

        if ($request->has('error')) {
            return redirect('/settings')
                ->with('error', 'Authorization denied: ' . $request->error);
        }

        $tokens = Motive::oauth()->exchangeCode($request->code);

        $request->user()->update([
            'motive_access_token' => $tokens->accessToken,
            'motive_refresh_token' => $tokens->refreshToken,
            'motive_token_expires_at' => $tokens->expiresAt,
        ]);

        session()->forget('motive_oauth_state');

        return redirect('/dashboard')
            ->with('success', 'Motive account connected!');
    }

    public function disconnect(Request $request)
    {
        $request->user()->update([
            'motive_access_token' => null,
            'motive_refresh_token' => null,
            'motive_token_expires_at' => null,
        ]);

        return redirect('/settings')
            ->with('success', 'Motive account disconnected');
    }
}
```

## Error Handling

Handle OAuth errors gracefully:

```php
use Motive\Exceptions\AuthenticationException;

try {
    $tokens = Motive::oauth()->exchangeCode($code);
} catch (AuthenticationException $e) {
    // Invalid or expired authorization code
    return redirect('/settings')
        ->with('error', 'Authorization failed. Please try again.');
}
```

# API Key Authentication

API key authentication is the simplest way to authenticate with the Motive API. It's ideal for server-to-server integrations where you manage a single fleet.

## Getting Your API Key

1. Log in to your [Motive Dashboard](https://gomotive.com)
2. Navigate to **Settings** > **API Access**
3. Click **Generate New API Key**
4. Copy the API key (you won't be able to see it again)
5. Store it securely in your environment

## Configuration

### Environment Variable

Add your API key to your `.env` file:

```env
MOTIVE_API_KEY=your-api-key-here
```

### Configuration File

The default configuration uses the API key from your environment:

```php
// config/motive.php
return [
    'connections' => [
        'default' => [
            'auth_driver' => 'api_key',
            'api_key' => env('MOTIVE_API_KEY'),
        ],
    ],
];
```

## Usage

### Using the Facade

```php
use Motive\Facades\Motive;

// Uses the configured API key automatically
$vehicles = Motive::vehicles()->list();
```

### Dynamic API Key

Override the configured API key at runtime:

```php
// Use a different API key for this request
$vehicles = Motive::withApiKey('different-api-key')
    ->vehicles()
    ->list();
```

This is useful when you need to access different Motive accounts dynamically.

## Security Best Practices

### Never Commit API Keys

Add `.env` to your `.gitignore`:

```gitignore
.env
.env.backup
.env.production
```

### Use Environment Variables

Always use environment variables, never hardcode:

```php
// GOOD
'api_key' => env('MOTIVE_API_KEY'),

// BAD - Never do this!
'api_key' => 'abc123def456',
```

### Rotate Keys Regularly

1. Generate a new API key in the Motive Dashboard
2. Update your environment with the new key
3. Deploy the change
4. Revoke the old API key

### Limit Key Scope

Request only the permissions your application needs when generating the API key.

### Monitor API Key Usage

- Review API usage in the Motive Dashboard
- Set up alerts for unusual activity
- Log all API calls in your application

## Troubleshooting

### AuthenticationException

If you receive an `AuthenticationException`:

```php
use Motive\Exceptions\AuthenticationException;

try {
    $vehicles = Motive::vehicles()->list();
} catch (AuthenticationException $e) {
    // Check if API key is configured
    if (empty(config('motive.connections.default.api_key'))) {
        Log::error('Motive API key not configured');
    } else {
        Log::error('Invalid Motive API key');
    }
}
```

**Common causes:**
- API key not set in `.env`
- Typo in the API key
- API key has been revoked
- Configuration cache needs clearing: `php artisan config:clear`

### Verify Configuration

Test your configuration:

```bash
php artisan tinker
>>> config('motive.connections.default.api_key')
=> "your-api-key-here"

>>> Motive\Facades\Motive::companies()->current()
=> Motive\Data\Company {#1234}
```

## API Key vs OAuth

| Feature | API Key | OAuth |
|---------|---------|-------|
| Setup complexity | Simple | Complex |
| User interaction | None | Required |
| Token expiration | Never | Yes |
| Per-user access | No | Yes |
| Best for | Server-to-server | Multi-tenant SaaS |

**Use API key authentication when:**
- You manage a single fleet
- Your application runs on a server
- No end-user authorization is needed

**Use OAuth when:**
- Building a multi-tenant SaaS application
- Users need to authorize access to their fleets
- You need user-specific access tokens

# Authentication

The Enlivy SDK supports two authentication methods: API Keys and OAuth 2.0.

## API Key Authentication

API keys are the simplest way to authenticate. Get your API key from your Enlivy dashboard.

### Global Configuration (Recommended)

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

// Set globally - all clients will use these settings
Enlivy::setApiKey('1|your_api_token');
Enlivy::setOrganizationId('org_xxx');

// Create client - inherits global config
$client = new EnlivyClient();

// Make requests
$invoices = $client->invoices->list();
```

### Per-Client Configuration

```php
<?php

use Enlivy\EnlivyClient;

// Configure per-client
$client = new EnlivyClient([
    'api_key' => '1|your_api_token',
    'organization_id' => 'org_xxx',
]);
```

### Multiple Organizations

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

// Set API key globally
Enlivy::setApiKey('1|your_api_token');

// Create clients for different organizations
$clientOrg1 = new EnlivyClient(['organization_id' => 'org_xxx']);
$clientOrg2 = new EnlivyClient(['organization_id' => 'org_yyy']);

// Or specify per-request
$client = new EnlivyClient();
$invoices = $client->invoices->list(['organization_id' => 'org_xxx']);
```

## OAuth 2.0 Authentication

For applications that need to act on behalf of users.

### With Access Token

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'access_token' => 'eat_xxx',
    'organization_id' => 'org_xxx',
]);
```

### With Auto-Refresh

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'access_token' => 'eat_xxx',
    'refresh_token' => 'ert_xxx',
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'organization_id' => 'org_xxx',
    'on_token_refresh' => function (string $newAccessToken, string $newRefreshToken) {
        // Store the new tokens in your database
        saveTokens($newAccessToken, $newRefreshToken);
    },
]);
```

### Global OAuth Configuration

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

Enlivy::configure([
    'access_token' => 'eat_xxx',
    'refresh_token' => 'ert_xxx',
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'organization_id' => 'org_xxx',
    'on_token_refresh' => function ($accessToken, $refreshToken) {
        saveTokens($accessToken, $refreshToken);
    },
]);

$client = new EnlivyClient();
```

## Configuration Options

### All Available Options

```php
<?php

use Enlivy\Enlivy;

Enlivy::configure([
    // Authentication
    'api_key' => '1|token',              // API key authentication
    'access_token' => 'eat_xxx',          // OAuth access token
    'refresh_token' => 'ert_xxx',         // OAuth refresh token
    'client_id' => 'oac_xxx',             // OAuth client ID
    'client_secret' => 'secret',          // OAuth client secret

    // Organization
    'organization_id' => 'org_xxx',       // Default organization

    // Network
    'api_base' => 'https://api.enlivy.com', // API base URL
    'timeout' => 30,                       // Request timeout (seconds)
    'max_network_retries' => 2,            // Retry count

    // SSL
    'verify_ssl_certs' => true,           // Verify SSL certificates
    'ca_bundle_path' => null,             // Custom CA bundle path

    // Callbacks
    'on_token_refresh' => null,           // Token refresh callback
]);
```

### Custom API Base (Self-Hosted)

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

// For self-hosted or development
Enlivy::setApiBase('https://api.your-domain.com');
Enlivy::setApiKey('1|token');

$client = new EnlivyClient();
```

## Environment-Based Configuration

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

// Load from environment variables
Enlivy::configure([
    'api_key' => getenv('ENLIVY_API_KEY'),
    'organization_id' => getenv('ENLIVY_ORGANIZATION_ID'),
    'api_base' => getenv('ENLIVY_API_BASE') ?: 'https://api.enlivy.com',
]);

$client = new EnlivyClient();
```

## Laravel Integration

```php
<?php
// config/enlivy.php

return [
    'api_key' => env('ENLIVY_API_KEY'),
    'organization_id' => env('ENLIVY_ORGANIZATION_ID'),
    'api_base' => env('ENLIVY_API_BASE', 'https://api.enlivy.com'),
];
```

```php
<?php
// app/Providers/EnlivyServiceProvider.php

namespace App\Providers;

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Illuminate\Support\ServiceProvider;

class EnlivyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EnlivyClient::class, function () {
            Enlivy::configure(config('enlivy'));
            return new EnlivyClient();
        });
    }
}
```

```php
<?php
// Usage in controller

use Enlivy\EnlivyClient;

class InvoiceController extends Controller
{
    public function __construct(
        private EnlivyClient $enlivy
    ) {}

    public function index()
    {
        $invoices = $this->enlivy->invoices->list();
        return view('invoices.index', compact('invoices'));
    }
}
```

## Testing

### Reset Configuration

```php
<?php

use Enlivy\Enlivy;

// In your test setUp
Enlivy::reset();

// Configure for tests
Enlivy::setApiKey('test_key');
Enlivy::setApiBase('http://localhost:8080');
```

### Get Current Configuration

```php
<?php

use Enlivy\Enlivy;

// Debug current settings
$config = Enlivy::toArray();
print_r($config);
```

## Security Best Practices

1. **Never commit API keys** - Use environment variables
2. **Rotate keys regularly** - Generate new keys periodically
3. **Use minimal permissions** - Create keys with only needed scopes
4. **Monitor usage** - Review API logs for suspicious activity
5. **Use OAuth for user actions** - API keys are for server-to-server

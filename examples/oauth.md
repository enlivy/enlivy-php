# OAuth Server

Build third-party applications that integrate with Enlivy using OAuth 2.0. Enlivy acts as an OAuth Provider allowing external apps (Zapier, Make, n8n) to connect to your data.

## Key Concepts

### Token Types

| Token Type | Prefix | TTL | Purpose |
|------------|--------|-----|---------|
| Client ID | `oac_` | N/A | Identifies your application |
| Authorization Code | `oauc_` | 10 min | Temporary code exchanged for tokens |
| Access Token | `eat_` | 1 hour | API authentication |
| Refresh Token | `ert_` | Never | Obtain new access tokens |

### Authorization Flow

```
Third-Party App         Enlivy API            User Browser
      |                      |                      |
      | 1. Redirect to /oauth/authorize             |
      |--------------------------------------------->|
      |                      |                      |
      |                      |  2. User logs in     |
      |                      |  3. Consent screen   |
      |                      |  4. User approves    |
      |                      |                      |
      |<------------------------------------|       |
      | 5. Redirect with code               |       |
      |                                             |
      | 6. POST /oauth/token                        |
      |-------------------->|                       |
      |                     |                       |
      |<--------------------|                       |
      | 7. Access + Refresh tokens                  |
```

## Managing OAuth Clients

### Create OAuth Client

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
]);

$oauthClient = $client->oauthClients->create([
    // Required
    'name_lang_map' => [
        'en' => 'My Integration App',
    ],
    'slug' => 'my-integration-app',
    'redirect_uris' => [
        'https://myapp.com/callback',
        'https://myapp.com/oauth/callback',
    ],

    // Optional
    'description_lang_map' => [
        'en' => 'Syncs invoices and prospects with MyApp',
    ],
    'homepage_url' => 'https://myapp.com',
    'privacy_policy_url' => 'https://myapp.com/privacy',
]);

echo "Client ID: {$oauthClient->id}\n";
echo "Client Secret: {$oauthClient->secret}\n"; // Store securely - only shown once!
```

### List OAuth Clients

```php
<?php

$clients = $client->oauthClients->list([
    'include' => ['user'],
]);

foreach ($clients as $oauthClient) {
    $name = $oauthClient->name_lang_map['en'] ?? 'Untitled';
    echo "{$name} ({$oauthClient->slug}): {$oauthClient->id}\n";
    echo "  Redirect URIs: " . implode(', ', $oauthClient->redirect_uris) . "\n";
    echo "  Active: " . ($oauthClient->is_active ? 'Yes' : 'No') . "\n";
}
```

### Retrieve OAuth Client

```php
<?php

$oauthClient = $client->oauthClients->retrieve('oac_xxx', [
    'include' => ['user'],
]);

echo "Client: {$oauthClient->id}\n";
echo "Name: " . ($oauthClient->name_lang_map['en'] ?? '') . "\n";
echo "Slug: {$oauthClient->slug}\n";
echo "Homepage: {$oauthClient->homepage_url}\n";
echo "Privacy Policy: {$oauthClient->privacy_policy_url}\n";
echo "Logo URL: {$oauthClient->logo_url}\n";
echo "First Party: " . ($oauthClient->is_first_party ? 'Yes' : 'No') . "\n";
echo "Verified: " . ($oauthClient->is_verified ? 'Yes' : 'No') . "\n";
echo "Active: " . ($oauthClient->is_active ? 'Yes' : 'No') . "\n";
```

### Update OAuth Client

```php
<?php

$oauthClient = $client->oauthClients->update('oac_xxx', [
    'name_lang_map' => [
        'en' => 'My Updated App',
    ],
    'redirect_uris' => [
        'https://myapp.com/callback',
        'https://myapp.com/oauth/callback',
        'https://staging.myapp.com/callback',
    ],
    'is_active' => true,
]);

echo "Updated: {$oauthClient->slug}\n";
```

### Delete OAuth Client

```php
<?php

$client->oauthClients->delete('oac_xxx');

echo "OAuth client deleted\n";
```

### Restore OAuth Client

```php
<?php

$client->oauthClients->restore('oac_xxx');

echo "OAuth client restored\n";
```

## Authorization Flow

### 1. Redirect User to Authorize

```php
<?php

$clientId = 'oac_xxx';
$redirectUri = 'https://myapp.com/callback';
$scopes = 'accounting:read prospects:read';
$state = bin2hex(random_bytes(16)); // CSRF protection

$authUrl = "https://api.enlivy.com/oauth/authorize?" . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => $scopes,
    'state' => $state,
]);

// Store state in session for verification
$_SESSION['oauth_state'] = $state;

// Redirect user
header("Location: {$authUrl}");
exit;
```

### 2. Handle Callback

```php
<?php
// callback.php

// Verify state
if ($_GET['state'] !== $_SESSION['oauth_state']) {
    die('Invalid state - possible CSRF attack');
}

$code = $_GET['code'];

// Exchange code for tokens
$ch = curl_init('https://api.enlivy.com/oauth/token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'grant_type' => 'authorization_code',
        'client_id' => 'oac_xxx',
        'client_secret' => 'your_client_secret',
        'code' => $code,
        'redirect_uri' => 'https://myapp.com/callback',
    ]),
    CURLOPT_RETURNTRANSFER => true,
]);
$response = json_decode(curl_exec($ch));
curl_close($ch);

$accessToken = $response->access_token;   // eat_xxx
$refreshToken = $response->refresh_token; // ert_xxx
$expiresIn = $response->expires_in;       // 3600 seconds

// Store tokens securely
saveTokens($accessToken, $refreshToken);
```

### 3. Use Access Token

```php
<?php

$client = new EnlivyClient([
    'access_token' => $accessToken,
    'organization_id' => 'org_xxx',
]);

$invoices = $client->invoices->list();
```

## Token Management

### Refresh Token

```php
<?php

$ch = curl_init('https://api.enlivy.com/oauth/token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'grant_type' => 'refresh_token',
        'client_id' => 'oac_xxx',
        'client_secret' => 'your_client_secret',
        'refresh_token' => $refreshToken,
    ]),
    CURLOPT_RETURNTRANSFER => true,
]);
$response = json_decode(curl_exec($ch));
curl_close($ch);

$newAccessToken = $response->access_token;
$newRefreshToken = $response->refresh_token;

// Update stored tokens
updateStoredTokens($newAccessToken, $newRefreshToken);
```

### Auto-Refresh with SDK

```php
<?php

$client = new EnlivyClient([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken,
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'organization_id' => 'org_xxx',
    'on_token_refresh' => function ($newAccess, $newRefresh) {
        // Update stored tokens automatically
        updateStoredTokens($newAccess, $newRefresh);
    },
]);

// Client will auto-refresh on 401
$invoices = $client->invoices->list();
```

### Revoke Token

```php
<?php

$ch = curl_init('https://api.enlivy.com/oauth/token/revoke');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'token' => $accessToken,
        'client_id' => 'oac_xxx',
        'client_secret' => 'your_client_secret',
    ]),
    CURLOPT_RETURNTRANSFER => true,
]);
curl_exec($ch);
curl_close($ch);
```

## Available Scopes

| Scope | Name | Description |
|-------|------|-------------|
| `accounting:read` | Read Accounting Data | View invoices, receipts, bank accounts, transactions, products, billing |
| `accounting:write` | Write Accounting Data | Create, update, delete accounting data |
| `prospects:read` | Read Prospects | View prospects, activities, and related data |
| `prospects:write` | Write Prospects | Create, update, delete prospects and activities |
| `contracts:read` | Read Contracts | View contracts, signatures, statuses |
| `contracts:write` | Write Contracts | Create, update, delete contracts |
| `webhooks:manage` | Manage Webhooks | Subscribe to real-time event notifications |
| `*` | Full Access | All scopes (wildcard) |

## Field Reference

### OAuth Client Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `name_lang_map` | object | App name by language (en required) |
| `slug` | string | Unique identifier (alpha-dash, max 100 chars) |
| `redirect_uris` | array | Array of allowed redirect URIs |

### OAuth Client Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `description_lang_map` | object | App description by language |
| `homepage_url` | string | App homepage URL |
| `privacy_policy_url` | string | Privacy policy URL |
| `is_active` | boolean | Whether client is active (update only) |

### OAuth Client Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Client ID (oac_xxx) |
| `user_id` | string | Owner user ID |
| `slug` | string | Unique slug |
| `name_lang_map` | object | App name by language |
| `description_lang_map` | object | App description by language |
| `redirect_uris` | array | Allowed redirect URIs |
| `allowed_scopes` | array | Restricted scopes (null = all) |
| `logo_file_extension` | string | Logo file extension |
| `logo_url` | string | Full logo URL |
| `homepage_url` | string | App homepage |
| `privacy_policy_url` | string | Privacy policy URL |
| `is_first_party` | boolean | First-party app flag |
| `is_active` | boolean | Active status |
| `is_verified` | boolean | Verified status |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

### Include Options

| Include | Description |
|---------|-------------|
| `user` | Owner user details |

## Complete Example: OAuth Integration

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

// 1. Create OAuth Client (one-time setup)
Enlivy::setApiKey('1|your_admin_token');
$adminClient = new EnlivyClient();

try {
    $oauthApp = $adminClient->oauthClients->create([
        'name_lang_map' => ['en' => 'Invoice Sync Pro'],
        'description_lang_map' => ['en' => 'Automatically sync invoices to your accounting software'],
        'slug' => 'invoice-sync-pro',
        'redirect_uris' => [
            'https://invoicesyncpro.com/oauth/callback',
        ],
        'homepage_url' => 'https://invoicesyncpro.com',
        'privacy_policy_url' => 'https://invoicesyncpro.com/privacy',
    ]);

    echo "OAuth App Created!\n";
    echo "Client ID: {$oauthApp->id}\n";
    echo "Client Secret: {$oauthApp->secret}\n";
    echo "\n";
    echo "IMPORTANT: Store the client secret securely.\n";
    echo "It cannot be retrieved later.\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}

// 2. Later, in your app's OAuth callback handler:
class OAuthCallbackHandler
{
    private string $clientId = 'oac_xxx';
    private string $clientSecret = 'your_stored_secret';
    private string $redirectUri = 'https://invoicesyncpro.com/oauth/callback';

    public function handleCallback(string $code, string $state): void
    {
        // Verify state matches session
        if ($state !== $_SESSION['oauth_state']) {
            throw new \Exception('Invalid state');
        }

        // Exchange code for tokens
        $tokens = $this->exchangeCodeForTokens($code);

        // Store tokens for this user
        $this->saveUserTokens(
            $tokens['access_token'],
            $tokens['refresh_token']
        );

        // Now you can make API calls
        $client = new EnlivyClient([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'organization_id' => 'org_xxx',
            'on_token_refresh' => fn ($at, $rt) => $this->saveUserTokens($at, $rt),
        ]);

        $invoices = $client->invoices->list();
        echo "Found " . count($invoices) . " invoices\n";
    }

    private function exchangeCodeForTokens(string $code): array
    {
        $ch = curl_init('https://api.enlivy.com/oauth/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ]),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response;
    }

    private function saveUserTokens(string $accessToken, string $refreshToken): void
    {
        // Save to your database
    }
}
```

## Security Best Practices

1. **Store secrets securely** - Never expose client secrets in client-side code
2. **Use HTTPS** - All redirect URIs must use HTTPS
3. **Validate state** - Always verify the state parameter to prevent CSRF
4. **Minimal scopes** - Request only the scopes you need
5. **Rotate tokens** - Use refresh tokens to obtain new access tokens
6. **Secure storage** - Encrypt tokens at rest in your database

## Related

- [Authentication](authentication.md) - API key vs OAuth
- [Webhooks](webhooks.md) - Get notified of events
- [Integrations](integrations.md) - Third-party service integrations

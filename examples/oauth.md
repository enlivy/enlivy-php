# OAuth Server

Build third-party applications that integrate with Enlivy using OAuth 2.0.

## Concepts

- **OAuth Client**: Your application registered with Enlivy
- **Authorization**: User grants your app access
- **Token**: Access and refresh tokens for API calls

## Managing OAuth Clients

### Create OAuth Client

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$oauthClient = $client->oauthClients->create([
    'name' => 'My Integration App',
    'redirect_uri' => 'https://myapp.com/callback',
    'scopes' => ['invoices:read', 'invoices:write', 'prospects:read'],
]);

echo "Client ID: {$oauthClient->id}\n";
echo "Client Secret: {$oauthClient->secret}\n"; // Store securely!
```

### List OAuth Clients

```php
<?php

$clients = $client->oauthClients->list();

foreach ($clients as $oauthClient) {
    echo "{$oauthClient->name}: {$oauthClient->id}\n";
}
```

## Authorization Flow

### 1. Redirect User to Authorize

```php
<?php

$clientId = 'oac_xxx';
$redirectUri = 'https://myapp.com/callback';
$scopes = 'invoices:read invoices:write';
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
    die('Invalid state');
}

$code = $_GET['code'];

// Exchange code for tokens
$response = (new EnlivyClient())->oauthTokens->create([
    'grant_type' => 'authorization_code',
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'code' => $code,
    'redirect_uri' => 'https://myapp.com/callback',
]);

$accessToken = $response->access_token;
$refreshToken = $response->refresh_token;
$expiresIn = $response->expires_in;

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

$response = $client->oauthTokens->create([
    'grant_type' => 'refresh_token',
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'refresh_token' => $refreshToken,
]);

$newAccessToken = $response->access_token;
$newRefreshToken = $response->refresh_token;
```

### Auto-Refresh with Callback

```php
<?php

$client = new EnlivyClient([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken,
    'client_id' => 'oac_xxx',
    'client_secret' => 'your_client_secret',
    'organization_id' => 'org_xxx',
    'on_token_refresh' => function ($newAccess, $newRefresh) {
        // Update stored tokens
        updateStoredTokens($newAccess, $newRefresh);
    },
]);

// Client will auto-refresh on 401
$invoices = $client->invoices->list();
```

## Managing Authorizations

### List Authorizations

```php
<?php

$authorizations = $client->oauthAuthorizations->list();

foreach ($authorizations as $auth) {
    echo "{$auth->client->name} - Scopes: " . implode(', ', $auth->scopes) . "\n";
}
```

### Revoke Authorization

```php
<?php

$client->oauthAuthorizations->delete('auth_xxx');
```

## Available Scopes

| Scope | Description |
|-------|-------------|
| `invoices:read` | Read invoices |
| `invoices:write` | Create/update invoices |
| `prospects:read` | Read prospects |
| `prospects:write` | Create/update prospects |
| `contracts:read` | Read contracts |
| `contracts:write` | Create/update contracts |
| `users:read` | Read organization users |
| `users:write` | Create/update users |

## Related

- [Authentication](authentication.md) - API key vs OAuth
- [Webhooks](webhooks.md) - Get notified of events

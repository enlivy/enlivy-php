# Enlivy PHP SDK

Official PHP client library for the [Enlivy API](https://enlivy.com).

## Requirements

- PHP 8.3+
- `ext-curl`, `ext-json`, `ext-mbstring`

## Installation

```bash
composer require enlivy/enlivy-php
```

## Quick Start

```php
$enlivy = new \Enlivy\EnlivyClient([
    'api_key' => '1|your_api_token',
    'organization_id' => 'org_xxx',
]);

// List prospects
$prospects = $enlivy->prospects->list();

foreach ($prospects as $prospect) {
    echo $prospect->title . "\n";
}

// Create
$prospect = $enlivy->prospects->create([
    'title' => 'New Lead',
    'email' => 'lead@example.com',
]);

// Retrieve
$prospect = $enlivy->prospects->retrieve('org_pros_xxx');

// Update
$enlivy->prospects->update('org_pros_xxx', ['title' => 'Updated']);

// Delete
$enlivy->prospects->delete('org_pros_xxx');
```

## Configuration

```php
$enlivy = new \Enlivy\EnlivyClient([
    'api_key' => '1|your_token',       // Required (or use OAuth)
    'organization_id' => 'org_xxx',    // Default org for requests
    'api_base' => 'https://api.enlivy.com', // Optional
    'timeout' => 30,                   // Optional (seconds)
]);
```

For OAuth authentication, see [examples/oauth.md](examples/oauth.md).

## Error Handling

```php
use Enlivy\Exception\{
    ValidationException,
    NotFoundException,
    AuthenticationException,
    RateLimitException,
};

try {
    $prospect = $enlivy->prospects->retrieve('invalid_id');
} catch (ValidationException $e) {
    $errors = $e->errors(); // ['field' => ['error message']]
} catch (NotFoundException $e) {
    // 404 - Resource not found
} catch (AuthenticationException $e) {
    // 401 - Invalid credentials
} catch (RateLimitException $e) {
    $retryAfter = $e->retryAfter(); // seconds
}
```

## Pagination

```php
$prospects = $enlivy->prospects->list(['page' => 1, 'per_page' => 25]);

echo "Page " . $prospects->getCurrentPage() . " of " . $prospects->getTotalPages();

foreach ($prospects as $prospect) {
    echo $prospect->title;
}
```

## Examples

See the [examples/](examples/) folder for detailed usage:

- [Authentication](examples/authentication.md)
- [OAuth](examples/oauth.md)
- [Invoices](examples/organization/invoices.md)
- [Prospects](examples/organization/prospects.md)
- [Contracts](examples/organization/contracts.md)
- [Projects](examples/organization/projects.md)
- [And more...](examples/README.md)

## Testing

```bash
./vendor/bin/phpunit              # Unit tests
./vendor/bin/phpstan analyse      # Static analysis
```

See [TESTING.md](TESTING.md) for integration tests.

## License

MIT License. See [LICENSE](LICENSE) for details.

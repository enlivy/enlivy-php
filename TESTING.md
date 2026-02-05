# Testing the Enlivy PHP SDK

This document explains how to run tests for the Enlivy PHP SDK.

## Test Suites

| Suite | Purpose | Requires API | Safe for Production |
|-------|---------|--------------|---------------------|
| **Unit** | Tests SDK logic with mocked HTTP | No | Yes |
| **Integration (Read-Only)** | Tests GET operations against real API | Yes | Yes |
| **Integration (CRUD)** | Tests full CRUD operations | Yes | **No** - creates/deletes data |

## Quick Start (Recommended)

The easiest way to configure integration tests is with a `.env.testing` file:

```bash
# 1. Copy the example config
cp .env.testing.example .env.testing

# 2. Edit with your credentials
nano .env.testing

# 3. Run integration tests
./vendor/bin/phpunit -c phpunit.integration.xml
```

Your `.env.testing` file should look like:

```env
ENLIVY_API_KEY="1|your_actual_token"
ENLIVY_ORGANIZATION_ID="org_xxx"
ENLIVY_API_BASE="http://enlivy_api.test"
# ENLIVY_ALLOW_WRITES="true"  # Uncomment to enable CRUD tests
```

> **Note**: `.env.testing` is gitignored - your credentials stay local.

## Running Tests

### Unit Tests (Default)

Unit tests use mocked HTTP responses and don't require API credentials:

```bash
# Run all unit tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

### Integration Tests

Integration tests run against a real Enlivy API instance.

#### Option 1: Using .env.testing (Recommended)

```bash
# Just run - credentials loaded from .env.testing
./vendor/bin/phpunit -c phpunit.integration.xml

# Run only read-only tests
./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/ReadOnlyTest.php

# Run CRUD tests (requires ENLIVY_ALLOW_WRITES=true in .env.testing)
./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/CrudTest.php
```

#### Option 2: Using Environment Variables

```bash
# Pass credentials directly
ENLIVY_API_KEY="1|your_token" \
ENLIVY_ORGANIZATION_ID="org_xxx" \
ENLIVY_API_BASE="http://enlivy_api.test" \
./vendor/bin/phpunit -c phpunit.integration.xml
```

#### Read-Only Tests (Safe)

These only perform GET operations - safe to run against any environment:

```bash
./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/ReadOnlyTest.php
```

#### CRUD Tests (Modifies Data)

**WARNING**: These tests CREATE, UPDATE, and DELETE real data.

Only run against a dedicated test organization:

```bash
# Enable writes in .env.testing:
# ENLIVY_ALLOW_WRITES="true"

# Then run:
./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/CrudTest.php
```

### All Integration Tests

```bash
# Read-only tests pass, CRUD tests skip (safe default)
./vendor/bin/phpunit -c phpunit.integration.xml

# All tests including CRUD (set ENLIVY_ALLOW_WRITES=true first)
./vendor/bin/phpunit -c phpunit.integration.xml
```

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `ENLIVY_API_KEY` | Yes | API key (format: `1\|token_string`) |
| `ENLIVY_ORGANIZATION_ID` | Yes | Organization ID (`org_xxx`) |
| `ENLIVY_API_BASE` | No | API base URL (default: `https://api.enlivy.com`) |
| `ENLIVY_ALLOW_WRITES` | No | Set to `true` to enable CRUD tests |

## Setting Up a Test Organization

For CRUD tests, we recommend creating a dedicated test organization:

1. Create a new organization in the Enlivy dashboard
2. Generate an API key for that organization
3. Use that organization's credentials for CRUD tests

This ensures tests don't interfere with real data.

## Continuous Integration

Example GitHub Actions workflow:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  unit-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: ./vendor/bin/phpunit

  integration-tests:
    runs-on: ubuntu-latest
    # Only run on main branch or PRs from maintainers
    if: github.ref == 'refs/heads/main'
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - name: Run read-only integration tests
        env:
          ENLIVY_API_KEY: ${{ secrets.ENLIVY_TEST_API_KEY }}
          ENLIVY_ORGANIZATION_ID: ${{ secrets.ENLIVY_TEST_ORG_ID }}
        run: ./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/ReadOnlyTest.php
```

## Writing New Tests

### Unit Tests

```php
use Enlivy\Tests\Unit\TestCase;

class MyServiceTest extends TestCase
{
    public function testSomething(): void
    {
        // Uses MockHttpClient automatically
        $client = $this->createMockClient([
            'object' => 'invoice',
            'id' => 'org_inv_xxx',
        ]);

        $invoice = $client->invoices->retrieve('org_inv_xxx');

        $this->assertEquals('org_inv_xxx', $invoice->id);
    }
}
```

### Integration Tests

```php
use Enlivy\Tests\Integration\IntegrationTestCase;

class MyIntegrationTest extends IntegrationTestCase
{
    public function testSomething(): void
    {
        $client = $this->getClient();

        // Real API call
        $invoices = $client->invoices->list();

        $this->assertInstanceOf(Collection::class, $invoices);
    }
}
```

## Test Coverage

Generate coverage report:

```bash
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage

# Open coverage/index.html in browser
```

## Troubleshooting

### Tests skip with "Environment variable required"

Create a `.env.testing` file with your credentials:

```bash
cp .env.testing.example .env.testing
# Edit .env.testing with your actual credentials
./vendor/bin/phpunit -c phpunit.integration.xml
```

Or set environment variables directly:

```bash
export ENLIVY_API_KEY="1|your_token"
export ENLIVY_ORGANIZATION_ID="org_xxx"
./vendor/bin/phpunit -c phpunit.integration.xml
```

### SSL certificate errors with local API

For local development without valid SSL:

```php
$client = new EnlivyClient([
    'api_key' => '...',
    'api_base' => 'http://enlivy_api.test', // Use HTTP, not HTTPS
]);
```

Or configure SSL verification:

```php
Enlivy::setVerifySslCerts(false);
```

### CRUD tests skip with "ENLIVY_ALLOW_WRITES required"

This is intentional. CRUD tests modify data, so explicit opt-in is required:

```bash
ENLIVY_ALLOW_WRITES=true ./vendor/bin/phpunit tests/Integration/CrudTest.php
```

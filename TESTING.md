# Testing the Enlivy PHP SDK

This document explains how to run tests for the Enlivy PHP SDK.

## Test Suites

| Suite | Purpose | Requires API | Config |
|-------|---------|--------------|--------|
| **Unit** | Tests SDK logic with mocked HTTP | No | `phpunit.xml` |
| **Integration** | Tests against real API (read-only) | Yes | `phpunit.integration.xml` |

## Quick Start

### Unit Tests (No API Required)

```bash
composer install
./vendor/bin/phpunit
```

### Integration Tests

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
```

> **Note**: `.env.testing` is gitignored - your credentials stay local.

## Integration Test Structure

Integration tests are organized by domain in `tests/Integration/View/`:

```
tests/Integration/
├── IntegrationTestCase.php     # Base class with helpers
└── View/                       # Read-only tests (list + retrieve)
    ├── InvoiceTest.php         # Invoices, prefixes
    ├── UserTest.php            # Organization users, roles
    ├── ProspectTest.php        # Prospects, statuses, activities
    ├── ContractTest.php        # Contracts, prefixes, statuses
    ├── ProductTest.php         # Products
    ├── TaxTest.php             # Tax classes, rates, types
    ├── ProjectTest.php         # Projects
    ├── ReceiptTest.php         # Receipts, prefixes
    ├── BankAccountTest.php     # Bank accounts, transactions
    ├── ReportTest.php          # Reports, schemas
    ├── ContentTest.php         # Guidelines, playbooks, files
    ├── ProposalTest.php        # Proposals, offers
    ├── WebhookTest.php         # Webhooks, tags
    └── TaskTest.php            # Tasks, task statuses
```

### Running Specific Tests

```bash
# All integration tests
./vendor/bin/phpunit -c phpunit.integration.xml

# Single test file
./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/View/InvoiceTest.php

# Single test method
./vendor/bin/phpunit -c phpunit.integration.xml --filter testListInvoices

# Tests matching a pattern
./vendor/bin/phpunit -c phpunit.integration.xml --filter Prospect
```

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `ENLIVY_API_KEY` | Yes | API key (format: `1\|token_string`) |
| `ENLIVY_ORGANIZATION_ID` | Yes | Organization ID (`org_xxx` or numeric) |
| `ENLIVY_API_BASE` | No | API base URL (default: `https://api.enlivy.com`) |

## Test Assertions

The `IntegrationTestCase` provides custom assertions:

```php
use Enlivy\Tests\Integration\IntegrationTestCase;

class MyTest extends IntegrationTestCase
{
    public function testExample(): void
    {
        $invoice = $this->getClient()->invoices->retrieve('org_inv_xxx');

        // Assert ID has correct prefix
        $this->assertIdPrefix('org_inv_', $invoice->id);

        // Standard PHPUnit assertions work too
        $this->assertNotNull($invoice->organization_id);
    }
}
```

## Running All Tests

```bash
# Unit tests only (fast, no API)
./vendor/bin/phpunit

# Integration tests only (requires API)
./vendor/bin/phpunit -c phpunit.integration.xml

# Static analysis
./vendor/bin/phpstan analyse

# All checks
composer install && \
./vendor/bin/phpunit && \
./vendor/bin/phpstan analyse
```

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
      - run: ./vendor/bin/phpstan analyse

  integration-tests:
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - name: Run integration tests
        env:
          ENLIVY_API_KEY: ${{ secrets.ENLIVY_TEST_API_KEY }}
          ENLIVY_ORGANIZATION_ID: ${{ secrets.ENLIVY_TEST_ORG_ID }}
          ENLIVY_API_BASE: ${{ secrets.ENLIVY_API_BASE }}
        run: ./vendor/bin/phpunit -c phpunit.integration.xml
```

## Writing New Tests

### Unit Tests

```php
namespace Enlivy\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Enlivy\EnlivyClient;
use Enlivy\Tests\Mock\MockHttpClient;

class MyServiceTest extends TestCase
{
    public function testSomething(): void
    {
        $mockHttp = new MockHttpClient([
            'id' => 'org_inv_xxx',
            'total' => 1000,
        ]);

        $client = new EnlivyClient([
            'api_key' => 'test',
            'http_client' => $mockHttp,
        ]);

        $invoice = $client->invoices->retrieve('org_inv_xxx');

        $this->assertEquals('org_inv_xxx', $invoice->id);
    }
}
```

### Integration Tests

```php
namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Invoice;
use Enlivy\Tests\Integration\IntegrationTestCase;

class MyIntegrationTest extends IntegrationTestCase
{
    public function testListItems(): void
    {
        $items = $this->getClient()->invoices->list();

        $this->assertInstanceOf(Collection::class, $items);
        $this->assertIsArray($items->data);

        if (count($items->data) > 0) {
            $this->assertInstanceOf(Invoice::class, $items->data[0]);
            $this->assertIdPrefix('org_inv_', $items->data[0]->id);
        }
    }

    public function testRetrieveItem(): void
    {
        $items = $this->getClient()->invoices->list(['per_page' => 1]);

        if (count($items->data) === 0) {
            $this->markTestSkipped('No invoices available for testing');
        }

        $invoice = $this->getClient()->invoices->retrieve($items->data[0]->id);

        $this->assertInstanceOf(Invoice::class, $invoice);
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

### Tests skip with "ENLIVY_API_KEY environment variable required"

Create a `.env.testing` file:

```bash
cp .env.testing.example .env.testing
# Edit with your credentials
```

Or set environment variables:

```bash
export ENLIVY_API_KEY="1|your_token"
export ENLIVY_ORGANIZATION_ID="org_xxx"
./vendor/bin/phpunit -c phpunit.integration.xml
```

### SSL certificate errors with local API

Use HTTP instead of HTTPS for local development:

```env
ENLIVY_API_BASE="http://enlivy_api.test"
```

### Tests fail with "Invalid include parameter"

The API validates include parameters. Check the error message for valid options:

```
Valid options are: organization, contract_parties, deleted_by_user
```

Update your test to use a valid include parameter.

### Many tests skipped

Tests skip when no data exists (e.g., no invoices in the test organization). This is expected for a fresh organization. Create some test data or run against a populated organization.

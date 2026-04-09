# Integrations

Connect Enlivy with third-party services like Stripe and ANAF (Romanian e-invoicing). Integrations use OAuth flows for secure connection.

## Key Concepts

### Integration Flow

1. **Initiate Connection** - Request OAuth connection with success/failure URLs
2. **User Authorization** - Redirect user to service's OAuth page
3. **Callback Handling** - Service redirects back with authorization
4. **Use Integration** - Make API calls using the connected service

## Stripe Integration

Connect Stripe for payment processing on invoices.

### Initiate Stripe Connection

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Start OAuth connection flow
$connection = $client->serviceIntegrationStripe->connect([
    'success_url' => 'https://yourapp.com/integrations/stripe/success',
    'failed_url' => 'https://yourapp.com/integrations/stripe/failed',
]);

// Redirect user to Stripe OAuth
// The response contains:
// - request_url: The URL to POST to
// - request_data: Data to include (organization_id, user_id, internal_secret)

echo "Redirect to: {$connection->request_url}\n";
```

### Stripe Webhook Callbacks

Monitor Stripe webhook events for your organization.

#### List Webhook Callbacks

```php
<?php

$callbacks = $client->stripeWebhookCallbacks->list([
    'per_page' => 50,
]);

foreach ($callbacks as $callback) {
    echo "Event: {$callback->name}\n";
    echo "  Account: {$callback->account}\n";
    echo "  Status: {$callback->status}\n";
    echo "  Processed: {$callback->processed_at}\n";

    if ($callback->summary) {
        echo "  Summary: " . json_encode($callback->summary) . "\n";
    }
}
```

#### Retrieve Webhook Callback

```php
<?php

$callback = $client->stripeWebhookCallbacks->retrieve('org_stripe_wh_xxx');

echo "Event: {$callback->name}\n";
echo "Account: {$callback->account}\n";
echo "URL: {$callback->url}\n";
echo "Status: {$callback->status}\n";
echo "Payload: " . json_encode($callback->payload) . "\n";
echo "Logs: " . json_encode($callback->logs) . "\n";
echo "Summary: " . json_encode($callback->summary) . "\n";
echo "Processed at: {$callback->processed_at}\n";
```

#### Delete Webhook Callback

```php
<?php

$client->stripeWebhookCallbacks->delete('org_stripe_wh_xxx');

echo "Webhook callback deleted\n";
```

### Stripe Webhook Callback Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Callback ID |
| `organization_id` | string | Organization ID |
| `name` | string | Event name (e.g., invoice.paid) |
| `account` | string | Stripe account ID |
| `url` | string | Webhook URL |
| `payload` | object | Event payload |
| `logs` | array | Processing logs |
| `summary` | object | Processing summary |
| `status` | string | Processing status (success, failed) |
| `processed_at` | datetime | When processed |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Update timestamp |

## ANAF Integration (Romania)

Connect ANAF for Romanian e-invoicing (SPV/e-Factura).

### Initiate ANAF Connection

```php
<?php

$connection = $client->serviceIntegrationAnaf->connect([
    'success_url' => 'https://yourapp.com/integrations/anaf/success',
    'failed_url' => 'https://yourapp.com/integrations/anaf/failed',
]);

// Redirect user to ANAF OAuth
echo "Redirect to: {$connection->request_url}\n";
```

## Invoice Network Exchanges

Track PEPPOL/e-invoice submissions and their status.

### List Network Exchanges

```php
<?php

$exchanges = $client->invoiceNetworkExchanges->list([
    'organization_invoice_id' => 'org_inv_xxx',
]);

foreach ($exchanges as $exchange) {
    echo "Network: {$exchange->network}\n";
    echo "  Status: {$exchange->status}\n";
    echo "  Submission ID: {$exchange->submission_id}\n";

    if ($exchange->error_message) {
        echo "  Error: {$exchange->error_message}\n";
    }
}
```

### Retrieve Network Exchange

```php
<?php

$exchange = $client->invoiceNetworkExchanges->retrieve('org_inv_peppol_xxx');

echo "Invoice: {$exchange->organization_invoice_id}\n";
echo "Network: {$exchange->network}\n";
echo "Status: {$exchange->status}\n";
echo "Submitted: {$exchange->submitted_at}\n";
echo "Processed: {$exchange->processed_at}\n";
```

## Complete Example: Stripe Payment Flow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create invoice with Stripe payment method
    $invoice = $client->invoices->create([
        'organization_receiver_user_id' => 'org_user_xxx',
        'currency' => 'EUR',
        'source' => 'internal',
        'direction' => 'outbound',
        'payment_method' => 'stripe_card_payment',
        'delivery_method' => 'email',
        'issued_at' => date('Y-m-d'),
        'due_at' => date('Y-m-d', strtotime('+30 days')),
        'line_items' => [
            [
                'name_lang_map' => ['en' => 'Monthly Subscription'],
                'quantity' => 1,
                'price' => 99.00,
                'type' => 'service',
            ],
        ],
    ]);

    echo "Invoice created: {$invoice->id}\n";
    echo "Payment method: {$invoice->payment_method}\n";

    // 2. Customer pays via Stripe (handled by customer portal)
    // ...

    // 3. Monitor webhook callbacks
    $callbacks = $client->stripeWebhookCallbacks->list([
        'per_page' => 10,
    ]);

    echo "\nRecent Stripe events:\n";
    foreach ($callbacks as $callback) {
        echo "  {$callback->name}: {$callback->status}\n";
    }

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Complete Example: Romanian E-Invoice (ANAF)

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create invoice for Romanian customer
    $invoice = $client->invoices->create([
        'organization_receiver_user_id' => 'org_user_romanian_xxx',
        'currency' => 'RON',
        'source' => 'internal',
        'direction' => 'outbound',
        'payment_method' => 'bank_transfer',
        'delivery_method' => 'email',
        'issued_at' => date('Y-m-d'),
        'due_at' => date('Y-m-d', strtotime('+30 days')),
        'line_items' => [
            [
                'name_lang_map' => ['ro' => 'Servicii consultanta'],
                'quantity' => 10,
                'price' => 500.00,
                'type' => 'service',
                'organization_tax_class_id' => 'org_tax_class_xxx', // 19% TVA
            ],
        ],
    ]);

    echo "Invoice created: {$invoice->id}\n";

    // 2. Push to ANAF e-Factura
    // Note: This requires ANAF integration to be connected
    $result = $client->invoices->peppolPush($invoice->id, 'anaf');

    echo "Submitted to ANAF!\n";
    echo "Submission ID: {$result->submission_id}\n";

    // 3. Check network exchange status
    $exchanges = $client->invoiceNetworkExchanges->list([
        'organization_invoice_id' => $invoice->id,
    ]);

    foreach ($exchanges as $exchange) {
        echo "\nANAF Status: {$exchange->status}\n";
        if ($exchange->error_message) {
            echo "Error: {$exchange->error_message}\n";
        }
    }

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Available Integrations

| Integration | Purpose | Region |
|-------------|---------|--------|
| Stripe | Payment processing | Global |
| ANAF | E-invoicing (e-Factura) | Romania |

## Security Notes

1. **OAuth Flow** - Integrations use secure OAuth flows, not API keys
2. **Success/Failed URLs** - Always provide HTTPS URLs for callbacks
3. **Webhook Verification** - Stripe webhooks are verified before processing
4. **Token Security** - Integration tokens are stored encrypted

## Related

- [Invoices](invoices.md) - Create invoices for payment
- [Webhooks](webhooks.md) - Get notified of integration events
- [OAuth](oauth.md) - OAuth authentication patterns

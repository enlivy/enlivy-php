# Integrations

Connect Enlivy with third-party services like Stripe and ANAF.

## Service Integrations

### List Available Integrations

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$integrations = $client->serviceIntegration->list();

foreach ($integrations as $integration) {
    $status = $integration->is_connected ? 'Connected' : 'Not connected';
    echo "{$integration->name}: {$status}\n";
}
```

## Stripe Integration

### Connect Stripe

```php
<?php

$stripe = $client->serviceIntegrationStripe->connect([
    'stripe_account_id' => 'acct_xxx',
    'stripe_publishable_key' => 'pk_live_xxx',
    'stripe_secret_key' => 'sk_live_xxx',
]);

echo "Stripe connected!\n";
```

### Check Stripe Status

```php
<?php

$stripe = $client->serviceIntegrationStripe->retrieve();

if ($stripe->is_connected) {
    echo "Connected to: {$stripe->stripe_account_id}\n";
}
```

### Create Invoice with Stripe Payment

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'stripe_card_payment', // Use Stripe
    'delivery_method' => 'email',
    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Subscription'],
            'quantity' => 1,
            'price' => 99.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],
]);
```

### Stripe Webhook Callbacks

Handle Stripe webhooks:

```php
<?php

$callbacks = $client->stripeWebhookCallbacks->list([
    'per_page' => 50,
]);

foreach ($callbacks as $callback) {
    echo "{$callback->event_type}: {$callback->status}\n";
}
```

## ANAF Integration (Romania)

For Romanian e-invoicing (SPV/e-Factura).

### Connect ANAF

```php
<?php

$anaf = $client->serviceIntegrationAnaf->connect([
    'certificate' => file_get_contents('/path/to/certificate.p12'),
    'certificate_password' => 'your_password',
    'cif' => 'RO12345678',
]);

echo "ANAF connected!\n";
```

### Check ANAF Status

```php
<?php

$anaf = $client->serviceIntegrationAnaf->retrieve();

if ($anaf->is_connected) {
    echo "Connected CIF: {$anaf->cif}\n";
    echo "Certificate expires: {$anaf->certificate_expires_at}\n";
}
```

### Submit Invoice to ANAF

```php
<?php

// Push invoice to e-Factura
$result = $client->invoices->peppolPush('org_inv_xxx', 'anaf');

echo "Submission ID: {$result->submission_id}\n";
echo "Status: {$result->status}\n";
```

### Check Invoice Network Exchange Status

```php
<?php

$exchanges = $client->invoiceNetworkExchanges->list([
    'filter' => ['organization_invoice_id' => 'org_inv_xxx'],
]);

foreach ($exchanges as $exchange) {
    echo "{$exchange->network}: {$exchange->status}\n";
    if ($exchange->error_message) {
        echo "  Error: {$exchange->error_message}\n";
    }
}
```

## Complete Example: Romanian E-Invoice

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

// 1. Ensure ANAF is connected
$anaf = $client->serviceIntegrationAnaf->retrieve();
if (!$anaf->is_connected) {
    die("ANAF not connected. Please configure integration first.\n");
}

// 2. Create invoice for Romanian customer
$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_romanian_xxx',
    'status' => 'sent',
    'currency' => 'RON',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',
    'issued_at' => date('Y-m-d'),
    'due_at' => date('Y-m-d', strtotime('+30 days')),
    'line_items' => [
        [
            'name_lang_map' => ['ro' => 'Servicii consultanță'],
            'quantity' => 10,
            'price' => 500.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_standard_xxx', // 19% TVA
        ],
    ],
]);

echo "Invoice created: {$invoice->number}\n";

// 3. Submit to e-Factura
$result = $client->invoices->peppolPush($invoice->id, 'anaf');

echo "Submitted to ANAF: {$result->submission_id}\n";

// 4. Check status later
$exchanges = $client->invoiceNetworkExchanges->list([
    'filter' => ['organization_invoice_id' => $invoice->id],
]);

foreach ($exchanges as $exchange) {
    echo "ANAF Status: {$exchange->status}\n";
}
```

## Related

- [Invoices](invoices.md) - Create invoices
- [Webhooks](webhooks.md) - Get notified of integration events

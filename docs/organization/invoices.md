# Invoices

Create, send, and manage invoices for your customers.

## Prerequisites

Before creating an invoice, you need:
1. An **OrganizationUser** with a role that has `can_be_invoiced = true` (the receiver)
2. Optionally, a **sender** OrganizationUser with `can_be_invoicing = true`
3. Optionally, **tax classes** for proper tax calculation

See [Organization Users](organization-users.md) to create customers first.

## Invoice Concepts

### Source & Direction

| Source | Direction | Description |
|--------|-----------|-------------|
| `internal` | `outbound` | You create and send to customer |
| `internal` | `inbound` | You create for what customer owes you |
| `external` | `outbound` | Upload invoice you sent elsewhere |
| `external` | `inbound` | Upload invoice you received |

### Status Flow

```
draft -> scheduled -> sent -> paid
                   \-> cancelled/void
```

### Available Statuses
- `draft` - Invoice is being prepared
- `scheduled` - Invoice will be sent at a future date
- `sent` - Invoice has been sent to the customer
- `paid` - Invoice has been paid
- `cancelled` - Invoice was cancelled before sending
- `void` - Invoice was voided after sending

### Invoice Types
- `standard` - Regular invoice
- `proforma` - Proforma invoice
- `credit_note` - Credit note

### Payment Methods
- `bank_transfer` - Bank transfer payment
- `stripe_card_payment` - Stripe card payment
- `cash` - Cash payment
- And others defined in the PaymentMethod helper

### Delivery Methods
- `email` - Send via email
- `print` - Print delivery
- And others defined in InvoiceDeliveryMethods enum

## Creating an Invoice

### Basic Invoice

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$invoice = $client->invoices->create([
    // Required fields
    'organization_receiver_user_id' => 'org_user_xxx', // Customer ID
    'status' => 'draft',
    'currency' => 'EUR',

    // Required for internal source invoices
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    // Line items (required for internal source)
    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Web Development Services'],
            'quantity' => 10,
            'price' => 75.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],

    // Optional defaults
    // 'source' => 'internal',     // defaults to internal
    // 'direction' => 'outbound',  // defaults to outbound
    // 'type' => 'standard',       // defaults to standard
]);

echo "Invoice created: {$invoice->id}\n";
echo "Number: {$invoice->number}\n";
echo "Total: {$invoice->total} {$invoice->currency}\n";
```

### Invoice with Multiple Line Items

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    // Dates
    'issued_at' => '2026-02-05',
    'due_at' => '2026-03-05',

    // Multiple line items
    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Website Design', 'ro' => 'Design Website'],
            'quantity' => 1,
            'price' => 500.00,
            'discount' => 0,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
            'order' => 1,
        ],
        [
            'name_lang_map' => ['en' => 'Development Hours'],
            'unit_lang_map' => ['en' => 'hour'],
            'quantity' => 20,
            'price' => 75.00,
            'discount' => 10, // Percentage discount
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
            'order' => 2,
        ],
        [
            'name_lang_map' => ['en' => 'Hosting (Annual)'],
            'quantity' => 1,
            'price' => 120.00,
            'type' => 'digital',
            'organization_tax_class_id' => 'org_tax_xxx',
            'order' => 3,
        ],
    ],

    // Notes (multilingual)
    'note_lang_map' => [
        'en' => 'Thank you for your business!',
        'ro' => 'Va multumim pentru colaborare!',
    ],
]);
```

### Line Item with Billing Period

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Monthly Subscription'],
            'quantity' => 1,
            'price' => 99.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',

            // Billing period for subscriptions
            'has_billing_period' => true,
            'billing_period_started_at' => '2026-02-01',
            'billing_period_ended_at' => '2026-02-28',
        ],
    ],
]);
```

### Line Item with Product Reference

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    'line_items' => [
        [
            // Reference existing product - inherits name, price, tax class
            'organization_product_id' => 'org_prod_xxx',
            'quantity' => 5,
            // Can override price if needed
            // 'price' => 150.00,
        ],
        [
            // Or define inline item
            'name_lang_map' => ['en' => 'Custom Work'],
            'quantity' => 1,
            'price' => 500.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],
]);
```

### Line Item with PEPPOL/E-Invoicing Fields

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    // PEPPOL references
    'peppol_project_reference' => 'PROJECT-2026-001',
    'peppol_purchase_order_reference' => 'PO-12345',
    'peppol_sales_order_reference' => 'SO-67890',

    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Consulting Services'],
            'quantity' => 10,
            'price' => 100.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',

            // PEPPOL/E-invoicing schema fields
            'invoice_schema_map' => [
                'classification_identifier_cpv' => '72000000', // CPV code
                'peppol_billing_unit_code' => 'HUR', // Hours
            ],
        ],
    ],
]);
```

### Invoice with Sender Identity

```php
<?php

$invoice = $client->invoices->create([
    // Sender (your company identity on the invoice)
    'organization_sender_user_id' => 'org_user_sender_xxx',

    // Receiver (customer)
    'organization_receiver_user_id' => 'org_user_customer_xxx',

    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    // Bank account for payment details on invoice
    'organization_bank_account_id' => 'org_bank_xxx',

    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Consulting Services'],
            'quantity' => 5,
            'price' => 200.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],
]);
```

### Invoice with Custom Identity Override

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    // Override receiver identity on invoice
    'receiver_has_custom_identity' => true,
    'receiver_custom_identity_is_business' => true,
    'receiver_custom_identity_name' => 'Acme Corp International',
    'receiver_custom_identity_organization_type' => 'LLC',

    // Additional receiver info
    'receiver_information' => [
        'registration_number' => '12345678',
    ],
    'receiver_country_code' => 'US',

    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Product'],
            'quantity' => 1,
            'price' => 100.00,
            'type' => 'service',
        ],
    ],
]);
```

### Invoice with Custom Numbering

```php
<?php

// Use an invoice prefix for custom numbering
$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_invoice_prefix_id' => 'org_inv_prefix_xxx', // e.g., "INV-2026-"
    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',
    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Product'],
            'quantity' => 1,
            'price' => 100.00,
            'type' => 'service',
            'organization_tax_class_id' => 'org_tax_xxx',
        ],
    ],
]);

echo "Invoice number: {$invoice->number}\n"; // e.g., "INV-2026-0001"
```

### Invoice with Stripe Payment

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'source' => 'internal',
    'direction' => 'outbound',
    'payment_method' => 'stripe_card_payment', // Stripe payment
    'delivery_method' => 'email',

    // Stripe-specific fields (optional, for linking existing charges)
    'payment_stripe_account_id' => 'acct_xxx',
    'payment_stripe_customer_id' => 'cus_xxx',
    // 'payment_stripe_charge_id' => 'ch_xxx',
    // 'payment_stripe_intent_id' => 'pi_xxx',

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

### Invoice with Bank Transfer QR Codes

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'organization_bank_account_id' => 'org_bank_xxx',
    'status' => 'draft',
    'currency' => 'EUR',
    'payment_method' => 'bank_transfer',
    'delivery_method' => 'email',

    // QR code types for bank transfer
    'bank_account_payment_qr_types' => ['epc', 'stuzza'], // European Payment Council, Austrian format

    'line_items' => [
        [
            'name_lang_map' => ['en' => 'Product'],
            'quantity' => 1,
            'price' => 100.00,
            'type' => 'service',
        ],
    ],
]);
```

## Listing Invoices

### Basic List

```php
<?php

$invoices = $client->invoices->list();

foreach ($invoices as $invoice) {
    echo "{$invoice->number}: {$invoice->total} {$invoice->currency} ({$invoice->status})\n";
}
```

### With Filters

```php
<?php

// Unpaid invoices
$unpaid = $client->invoices->list([
    'status' => ['draft', 'sent'],
]);

// Invoices for specific customer
$customerInvoices = $client->invoices->list([
    'organization_receiver_user_id' => 'org_user_xxx',
]);

// Invoices from date range
$thisMonth = $client->invoices->list([
    'issued_at_from' => '2026-02-01',
    'issued_at_to' => '2026-02-28',
]);

// Overdue invoices
$overdue = $client->invoices->list([
    'status' => 'sent',
    'due_at_to' => date('Y-m-d'), // Due before today
]);
```

### With Related Data

```php
<?php

$invoices = $client->invoices->list([
    'include' => ['receiver_user', 'sender_user', 'line_items', 'bank_account'],
]);

foreach ($invoices as $invoice) {
    $customerName = $invoice->receiver_user->name
        ?? "{$invoice->receiver_user->first_name} {$invoice->receiver_user->last_name}";

    echo "{$invoice->number} - {$customerName}\n";

    foreach ($invoice->line_items ?? [] as $item) {
        $name = $item->name_lang_map['en'] ?? array_values($item->name_lang_map)[0] ?? 'Item';
        echo "  - {$name}: {$item->quantity} x {$item->price}\n";
    }
}
```

## Retrieving an Invoice

```php
<?php

$invoice = $client->invoices->retrieve('org_inv_xxx', [
    'include' => ['receiver_user', 'line_items', 'notification_logs'],
]);

echo "Invoice: {$invoice->number}\n";
echo "Customer: {$invoice->receiver_user->email}\n";
echo "Status: {$invoice->status}\n";
echo "Sub Total: {$invoice->sub_total} {$invoice->currency}\n";
echo "Tax: {$invoice->tax_total} {$invoice->currency}\n";
echo "Total: {$invoice->total} {$invoice->currency}\n";
```

## Updating an Invoice

```php
<?php

// Update draft invoice
$invoice = $client->invoices->update('org_inv_xxx', [
    'due_at' => '2026-04-01',
    'note_lang_map' => [
        'en' => 'Updated payment terms: Net 60',
    ],
]);

// Change status
$invoice = $client->invoices->update('org_inv_xxx', [
    'status' => 'sent',
    'issued_at' => date('Y-m-d'),
]);

// Mark as paid
$invoice = $client->invoices->update('org_inv_xxx', [
    'status' => 'paid',
    'paid_at' => date('Y-m-d H:i:s'),
]);
```

## Sending an Invoice

### Via Email

```php
<?php

$result = $client->invoices->email('org_inv_xxx', [
    'to' => ['customer@example.com'],
    'cc' => ['accounting@mycompany.com'],
    'subject' => 'Invoice #INV-2026-0001 from Acme Corp',
    'message' => 'Please find attached your invoice. Payment is due within 30 days.',
]);

echo "Email sent successfully\n";
```

### Via PEPPOL (E-Invoicing)

```php
<?php

// Send to Romanian ANAF (e-Factura)
$result = $client->invoices->peppolPush('org_inv_xxx', 'anaf');

// Send to other PEPPOL networks
$result = $client->invoices->peppolPush('org_inv_xxx', 'peppol');
```

## Downloading Invoice PDF

```php
<?php

// Download as PDF
$pdf = $client->invoices->download('org_inv_xxx');

// Save to file
file_put_contents('invoice.pdf', $pdf);

// Or stream to browser
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="invoice.pdf"');
echo $pdf;
```

## Deleting an Invoice

```php
<?php

// Soft delete
$invoice = $client->invoices->delete('org_inv_xxx');

echo "Deleted at: {$invoice->deleted_at}\n";
```

## Restoring a Deleted Invoice

```php
<?php

$invoice = $client->invoices->restore('org_inv_xxx');

echo "Invoice restored: {$invoice->number}\n";
```

## Tagging Invoices

```php
<?php

// Add tags
$invoice = $client->invoices->tag('org_inv_xxx', [
    'tags' => ['priority', 'q1-2026'],
]);

// Remove tags
$invoice = $client->invoices->untag('org_inv_xxx', [
    'tags' => ['priority'],
]);
```

## Working with Invoice Prefixes

```php
<?php

// List prefixes
$prefixes = $client->invoicePrefixes->list();

// Create a new prefix
$prefix = $client->invoicePrefixes->create([
    'name' => 'Standard Invoices',
    'alias' => 'INV',
    'type' => 'standard', // standard, proforma, credit_note
    'next_number' => 1,
    'padding' => 4, // INV-0001
]);
```

## External Invoices

External invoices represent documents created **outside** Enlivy — vendor invoices you received,
invoices sent through another system, etc. They differ from internal invoices in several ways:

| | Internal | External |
|---|---|---|
| **Line items** | Required | Not required |
| **Payment method** | Required | Not required |
| **Delivery method** | Required | Not required |
| **Totals** | Auto-calculated from line items | Manually specified |
| **File attachment** | Not applicable | Optional (PDF, image, document) |

### Create an External Invoice

```php
<?php

$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'sent',
    'currency' => 'EUR',
    'source' => 'external', // Uploaded, not generated
    'direction' => 'inbound', // Received from someone

    // External invoice details
    'number' => 'VENDOR-2026-123',
    'issued_at' => '2026-01-15',
    'due_at' => '2026-02-15',

    // Totals (line_items, payment_method, delivery_method not required for external)
    'sub_total' => 1000.00,
    'tax_total' => 190.00,
    'discount' => 0,
    'total' => 1190.00,

    // External invoices can have tax breakdown
    'taxes' => [
        [
            'organization_tax_class_id' => 'org_tax_xxx',
            'organization_tax_rate_id' => 'org_tax_rate_xxx',
            'amount' => 190.00,
        ],
    ],
]);
```

### Attach a File to an External Invoice

File uploads are only supported for **external** invoices (`source = 'external'`).
Internal invoices generate their own PDF — they don't accept file attachments.

Accepted file types: PDF, PNG, JPEG, DOC, DOCX, TXT.

```php
<?php

// Upload file when creating an external invoice
$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    'status' => 'sent',
    'currency' => 'EUR',
    'source' => 'external',
    'direction' => 'inbound',
    'number' => 'VENDOR-2026-456',
    'issued_at' => '2026-03-01',
    'due_at' => '2026-04-01',
    'sub_total' => 500.00,
    'tax_total' => 95.00,
    'total' => 595.00,

    // Attach the original document
    'file' => new \CURLFile('/path/to/vendor-invoice.pdf', 'application/pdf', 'vendor-invoice.pdf'),
]);
```

```php
<?php

// Attach or replace a file on an existing external invoice
$invoice = $client->invoices->update('org_inv_xxx', [
    'file' => new \CURLFile('/path/to/scan.jpg', 'image/jpeg', 'scan.jpg'),
]);
```

## Complete Example: Full Invoicing Workflow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Ensure customer exists (see organization-users.md)
    $customerId = 'org_user_customer_xxx';

    // 2. Get tax class
    $taxClasses = $client->taxClasses->list();
    $standardTax = $taxClasses->data[0];

    // 3. Create invoice
    $invoice = $client->invoices->create([
        'organization_receiver_user_id' => $customerId,
        'status' => 'draft',
        'currency' => 'EUR',
        'source' => 'internal',
        'direction' => 'outbound',
        'payment_method' => 'bank_transfer',
        'delivery_method' => 'email',
        'due_at' => date('Y-m-d', strtotime('+30 days')),
        'line_items' => [
            [
                'name_lang_map' => ['en' => 'Monthly Subscription'],
                'quantity' => 1,
                'price' => 99.00,
                'type' => 'service',
                'organization_tax_class_id' => $standardTax->id,
            ],
        ],
        'note_lang_map' => [
            'en' => 'Thank you for your business!',
        ],
    ]);

    echo "Created invoice: {$invoice->number}\n";
    echo "Total: {$invoice->total} {$invoice->currency}\n";

    // 4. Download PDF for review
    $pdf = $client->invoices->download($invoice->id);
    file_put_contents("invoice-{$invoice->number}.pdf", $pdf);

    // 5. Send to customer
    $invoice = $client->invoices->update($invoice->id, [
        'status' => 'sent',
        'issued_at' => date('Y-m-d'),
    ]);

    $client->invoices->email($invoice->id, [
        'to' => ['customer@example.com'],
        'subject' => "Invoice {$invoice->number}",
        'message' => 'Please find your invoice attached.',
    ]);

    echo "Invoice sent to customer!\n";

    // 6. Later: Mark as paid
    // $client->invoices->update($invoice->id, [
    //     'status' => 'paid',
    //     'paid_at' => date('Y-m-d H:i:s'),
    // ]);

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Organization Users](organization-users.md) - Create customers
- [Products](products.md) - Manage product catalog
- [Taxes](taxes.md) - Configure tax classes
- [Receipts](receipts.md) - Create receipts for payments

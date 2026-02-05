# Proposals

Send professional proposals to prospects or customers. Proposals can be based on offer templates or created with custom payments and line items.

## Key Concepts

### Proposal Structure

```
Proposal
    |
    +-- Payments (payment schedule)
    |       |
    |       +-- Line Items (products/services per payment)
    |
    +-- Recipient (prospect, org user, or email)
    +-- Optional: Contract requirement
```

### Recipients

Proposals can be sent to:
- An organization prospect (`organization_prospect_id`)
- An organization user (`organization_receiver_user_id`)
- An email recipient (`recipient_email` + `recipient_name`)

Note: Email recipient is mutually exclusive with prospect/user.

## Creating Proposals

### Basic Proposal from Offer

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$proposal = $client->proposals->create([
    // Based on offer template
    'organization_offer_id' => 'org_offr_xxx',
    'organization_offer_payment_plan_id' => 'org_offr_plan_xxx',

    // Recipient
    'organization_prospect_id' => 'org_pros_xxx',

    // Required
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',
]);

echo "Proposal created: {$proposal->id}\n";
```

### Custom Proposal with Payments

```php
<?php

$proposal = $client->proposals->create([
    // Recipient
    'organization_prospect_id' => 'org_pros_xxx',

    // Required
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    // Optional note
    'note_lang_map' => [
        'en' => 'Thank you for your interest. Please find our proposal below.',
    ],

    // Payment schedule with line items
    'payments' => [
        [
            'name_lang_map' => ['en' => 'Initial Payment (50%)'],
            'days_after_order' => 0, // Due immediately
            'order' => 1,
            'line_items' => [
                [
                    'name_lang_map' => ['en' => 'Design Phase'],
                    'description_lang_map' => ['en' => 'UX/UI design and prototyping'],
                    'unit_lang_map' => ['en' => 'service'],
                    'type' => 'service',
                    'quantity' => 1,
                    'price' => 5000.00,
                    'order' => 1,
                ],
            ],
        ],
        [
            'name_lang_map' => ['en' => 'Final Payment (50%)'],
            'days_after_order' => 30, // Due 30 days after order
            'order' => 2,
            'line_items' => [
                [
                    'name_lang_map' => ['en' => 'Development & Launch'],
                    'description_lang_map' => ['en' => 'Full development and deployment'],
                    'type' => 'service',
                    'quantity' => 1,
                    'price' => 5000.00,
                    'order' => 1,
                ],
            ],
        ],
    ],
]);
```

### Proposal to Email Recipient

```php
<?php

$proposal = $client->proposals->create([
    // Email recipient (exclusive - cannot use with prospect/user)
    'recipient_email' => 'prospect@example.com',
    'recipient_name' => 'John Doe',

    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    'payments' => [
        [
            'name_lang_map' => ['en' => 'Full Payment'],
            'days_after_order' => 0,
            'order' => 1,
            'line_items' => [
                [
                    'name_lang_map' => ['en' => 'Consulting Services'],
                    'type' => 'service',
                    'quantity' => 10,
                    'price' => 150.00,
                    'order' => 1,
                ],
            ],
        ],
    ],
]);
```

### Proposal to Organization User

```php
<?php

$proposal = $client->proposals->create([
    // Send to existing organization user
    'organization_receiver_user_id' => 'org_user_xxx',

    // Optional sender
    'organization_sender_user_id' => 'org_user_sender_xxx',

    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    'payments' => [
        // ... payment schedule
    ],
]);
```

### Proposal with Contract Requirement

```php
<?php

$proposal = $client->proposals->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    // Require contract signing
    'contract_is_required' => true,
    'contract_trigger' => 'on_acceptance', // or 'manual'
    'contract_default_sender_user_id' => 'org_user_xxx',

    'payments' => [
        // ... payment schedule
    ],
]);
```

### Proposal with Payment Methods

```php
<?php

$proposal = $client->proposals->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    // Allowed payment methods
    'allowed_payment_methods' => [
        'bank_transfer',
        'card',
        'stripe',
    ],

    'payments' => [
        // ... payment schedule
    ],
]);
```

### Proposal with Product References

Use existing products in line items:

```php
<?php

$proposal = $client->proposals->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    'payments' => [
        [
            'name_lang_map' => ['en' => 'Payment'],
            'days_after_order' => 0,
            'order' => 1,
            'line_items' => [
                [
                    // Reference existing product
                    'organization_product_id' => 'org_prod_xxx',
                    'quantity' => 5,
                    'order' => 1,
                ],
                [
                    // Custom line item
                    'name_lang_map' => ['en' => 'Custom Work'],
                    'type' => 'service',
                    'quantity' => 1,
                    'price' => 500.00,
                    'order' => 2,
                ],
            ],
        },
    ],
]);
```

### Proposal with Optional Items

```php
<?php

$proposal = $client->proposals->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    'payments' => [
        [
            'name_lang_map' => ['en' => 'Payment'],
            'days_after_order' => 0,
            'order' => 1,
            'line_items' => [
                [
                    'name_lang_map' => ['en' => 'Core Package'],
                    'type' => 'service',
                    'quantity' => 1,
                    'price' => 5000.00,
                    'is_optional' => false, // Required
                    'order' => 1,
                ],
                [
                    'name_lang_map' => ['en' => 'Premium Support'],
                    'type' => 'service',
                    'quantity' => 1,
                    'price' => 1000.00,
                    'is_optional' => true, // Client can choose
                    'order' => 2,
                ],
            ],
        },
    ],
]);
```

### Proposal with Tax Classes

```php
<?php

$proposal = $client->proposals->create([
    'organization_prospect_id' => 'org_pros_xxx',
    'currency' => 'EUR',
    'expires_at' => '2026-03-05',

    'payments' => [
        [
            'name_lang_map' => ['en' => 'Payment'],
            'days_after_order' => 0,
            'order' => 1,
            'line_items' => [
                [
                    'name_lang_map' => ['en' => 'Consulting'],
                    'type' => 'service',
                    'quantity' => 10,
                    'price' => 100.00,
                    'organization_tax_class_id' => 'org_tax_class_xxx',
                    'order' => 1,
                ],
            ],
        },
    ],
]);
```

## Listing Proposals

```php
<?php

$proposals = $client->proposals->list([
    'include' => ['offer', 'prospect', 'receiver_user', 'payments'],
]);

foreach ($proposals as $proposal) {
    echo "Proposal: {$proposal->id}\n";
    echo "  Currency: {$proposal->currency}\n";
    echo "  Expires: {$proposal->expires_at}\n";
    echo "  Total: {$proposal->total}\n";
}
```

### Filter by Prospect

```php
<?php

$proposals = $client->proposals->list([
    'filter' => [
        'organization_prospect_id' => 'org_pros_xxx',
    ],
]);
```

### Filter by Status

```php
<?php

// Accepted proposals
$accepted = $client->proposals->list([
    'filter' => [
        'status' => 'accepted',
    ],
]);

// Pending proposals
$pending = $client->proposals->list([
    'filter' => [
        'status' => 'pending',
    ],
]);
```

## Retrieving a Proposal

```php
<?php

$proposal = $client->proposals->retrieve('org_prop_xxx', [
    'include' => ['offer', 'prospect', 'payments', 'payments.line_items'],
]);

echo "Proposal: {$proposal->id}\n";
echo "Status: {$proposal->status}\n";
echo "Sub Total: {$proposal->sub_total}\n";
echo "Discount: {$proposal->discount}\n";
echo "Total: {$proposal->total}\n";
echo "Expires: {$proposal->expires_at}\n";

// Check status timestamps
if ($proposal->sent_at) {
    echo "Sent: {$proposal->sent_at}\n";
}
if ($proposal->viewed_at) {
    echo "Viewed: {$proposal->viewed_at}\n";
}
if ($proposal->accepted_at) {
    echo "Accepted: {$proposal->accepted_at}\n";
}
if ($proposal->rejected_at) {
    echo "Rejected: {$proposal->rejected_at}\n";
}

// Display payments
foreach ($proposal->payments as $payment) {
    echo "\nPayment: " . ($payment->name_lang_map['en'] ?? 'Untitled') . "\n";
    echo "  Due: {$payment->days_after_order} days after order\n";

    foreach ($payment->line_items as $item) {
        $name = $item->name_lang_map['en'] ?? 'Item';
        echo "  - {$item->quantity} x {$name} @ {$item->price}\n";
    }
}
```

## Updating a Proposal

```php
<?php

$proposal = $client->proposals->update('org_prop_xxx', [
    'expires_at' => '2026-04-05', // Extend expiration
    'note_lang_map' => [
        'en' => 'Updated proposal with extended timeline.',
    ],
]);

echo "Updated proposal: {$proposal->id}\n";
```

## Deleting a Proposal

```php
<?php

$proposal = $client->proposals->delete('org_prop_xxx');

echo "Deleted at: {$proposal->deleted_at}\n";
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `currency` | string | ISO 4217 currency code |
| `expires_at` | date | Expiration date (must be future, within 1 year) |

### Recipient Fields (choose one approach)

| Field | Type | Description |
|-------|------|-------------|
| `organization_prospect_id` | string | Prospect ID (prohibits recipient_email) |
| `organization_receiver_user_id` | string | Receiver user ID (prohibits recipient_email) |
| `recipient_email` | string | Email recipient (prohibits prospect/user) |
| `recipient_name` | string | Name for email recipient |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `organization_offer_id` | string | Base offer template |
| `organization_offer_payment_plan_id` | string | Selected payment plan from offer |
| `organization_project_id` | string | Link to project |
| `organization_sender_user_id` | string | Sender user ID |
| `note_lang_map` | object | Note by language |
| `allowed_payment_methods` | array | Allowed payment methods |
| `contract_is_required` | boolean | Require contract signing |
| `contract_trigger` | string | When to trigger contract (on_acceptance, manual) |
| `contract_default_sender_user_id` | string | Default contract sender |
| `payments` | array | Payment schedule |

### Payment Object Fields

| Field | Type | Description |
|-------|------|-------------|
| `name_lang_map` | object | Payment name by language |
| `days_after_order` | integer | Days after order when due |
| `order` | integer | Sort order |
| `line_items` | array | Line items in this payment |

### Line Item Fields

| Field | Type | Description |
|-------|------|-------------|
| `organization_product_id` | string | Product reference |
| `name_lang_map` | object | Name by language (required if no product) |
| `description_lang_map` | object | Description by language |
| `unit_lang_map` | object | Unit by language |
| `type` | string | Product type (service, digital, physical, bonus) |
| `quantity` | numeric | Quantity |
| `price` | numeric | Unit price |
| `discount` | numeric | Discount amount |
| `organization_tax_class_id` | string | Tax class ID |
| `order` | integer | Sort order |
| `is_optional` | boolean | Whether item is optional |
| `invoice_schema_map` | object | PEPPOL e-invoicing fields |

## Complete Example: Proposal Workflow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Get prospect
    $prospect = $client->prospects->retrieve('org_pros_xxx');

    // 2. Create proposal
    $proposal = $client->proposals->create([
        'organization_prospect_id' => $prospect->id,
        'currency' => 'EUR',
        'expires_at' => date('Y-m-d', strtotime('+14 days')),

        'note_lang_map' => [
            'en' => "Hi {$prospect->first_name}, thank you for your interest.",
        ],

        'allowed_payment_methods' => ['bank_transfer', 'card'],

        'payments' => [
            [
                'name_lang_map' => ['en' => 'Upfront Payment (50%)'],
                'days_after_order' => 0,
                'order' => 1,
                'line_items' => [
                    [
                        'name_lang_map' => ['en' => 'Project Setup & Design'],
                        'description_lang_map' => ['en' => 'Initial setup and design phase'],
                        'type' => 'service',
                        'quantity' => 1,
                        'price' => 2500.00,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'name_lang_map' => ['en' => 'Completion Payment (50%)'],
                'days_after_order' => 30,
                'order' => 2,
                'line_items' => [
                    [
                        'name_lang_map' => ['en' => 'Development & Delivery'],
                        'description_lang_map' => ['en' => 'Full development and handoff'],
                        'type' => 'service',
                        'quantity' => 1,
                        'price' => 2500.00,
                        'order' => 1,
                    ],
                ],
            ],
        ],
    ]);

    echo "Proposal created!\n";
    echo "ID: {$proposal->id}\n";
    echo "Total: {$proposal->total} {$proposal->currency}\n";
    echo "Expires: {$proposal->expires_at}\n";

    // 3. Log activity on prospect
    $client->prospectActivities->create([
        'organization_prospect_id' => $prospect->id,
        'type' => 'email',
        'title_lang_map' => ['en' => 'Proposal sent'],
        'description_lang_map' => ['en' => "Sent proposal #{$proposal->id}"],
    ]);

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Offers](offers.md) - Create offer templates
- [Prospects](prospects.md) - Send proposals to prospects
- [Contracts](contracts.md) - Create contracts after acceptance
- [Invoices](invoices.md) - Invoice based on accepted proposal

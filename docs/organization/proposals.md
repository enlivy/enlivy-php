# Proposals

Send professional proposals to prospects or customers. Proposals can be based on billing package templates or created with custom payments and line items.

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

### Proposal from Billing Package

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$proposal = $client->proposals->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    'organization_billing_package_payment_plan_id' => 'org_bp_plan_xxx',

    // Subscription packages: pin a cadence variant (omit = the package default)
    // 'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',

    // Recipient
    'organization_prospect_id' => 'org_pros_xxx',
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
                    'unit_code' => 'H87',
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
    'include' => ['billing_package', 'prospect', 'receiver_user', 'payments'],
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
    'organization_prospect_id' => 'org_pros_xxx',
]);
```

### Filter by Status

```php
<?php

// Accepted proposals
$accepted = $client->proposals->list([
    'status' => 'accepted',
]);

// Pending proposals
$pending = $client->proposals->list([
    'status' => 'pending',
]);
```

## Retrieving a Proposal

```php
<?php

$proposal = $client->proposals->retrieve('org_prop_xxx', [
    'include' => ['billing_package', 'prospect', 'payments', 'payments.line_items'],
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

## Creating a Billing Schedule

Manually create a billing schedule from an **accepted** subscription proposal. This is only valid
while the proposal's `can_create_billing_schedule` is `true` (a schedule has not already been
created). All body fields are optional:

```php
<?php

$proposal = $client->proposals->createBillingSchedule('org_prop_xxx', [
    'start_at' => '2026-04-01',
    'max_occurrences' => 12,
    'payment_method' => 'bank_transfer',
    'organization_user_payment_method_id' => 'org_user_pm_xxx',
    'organization_bank_account_id' => 'org_bank_xxx',
    'name_lang_map' => ['en' => 'Membership'],
    'currency' => 'EUR',
    'currency_conversion_fee' => 2.5,
]);

echo "Billing schedule created for proposal: {$proposal->id}\n";
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
| `organization_billing_package_id` | string | Base billing package |
| `organization_billing_package_payment_plan_id` | string | Selected payment plan from billing package (one_time) |
| `organization_billing_package_subscription_term_id` | string | Selected subscription cadence variant from billing package (subscription) |
| `billed_currency` | string | Currency the accepted proposal is billed in (also returned on read) |
| `allowed_currencies` | array | Currencies the proposal may settle in — `currency` and `billed_currency` must both be in it |
| `exchange_rate_guarantee` | string | When the conversion rate is fixed: `invoice` or `acceptance` |
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
| `unit_code` | string | Billing unit as a UN/ECE code (`HUR`, `DAY`, `H87`, …) |
| `type` | string | Product type (service, digital, physical, bonus) |
| `quantity` | numeric | Quantity |
| `price` | numeric | Unit price |
| `discount` | numeric | Discount amount |
| `organization_tax_class_id` | string | Tax class ID |
| `order` | integer | Sort order |
| `is_optional` | boolean | Whether item is optional |
| `invoice_schema_map` | object | PEPPOL e-invoicing fields |

### Read-Only Fields

Returned on read, not writable:

| Field | Type | Description |
|-------|------|-------------|
| `can_create_billing_schedule` | boolean | Whether a billing schedule can still be created from this (accepted subscription) proposal |
| `has_unsigned_required_contracts` | boolean | Whether required contracts remain unsigned |
| `billed_currency` | string\|null | Currency the proposal is billed in |
| `billed_currency_is_choosable` | boolean | Whether the customer may still pick the settlement currency |
| `billed_conversion` | object\|null | The held conversion — see [Settlement Currency](#settlement-currency) |
| `portal_url` | string\|null | Customer-portal address for this proposal |
| `stage` | string | Where the proposal sits — see [Stages](#stages) |
| `outcome_mode` | string\|null | What the proposal settles into — see below |

`outcome_mode` is inherited from the billing package the proposal was built from, or set once on
`create()` for a custom proposal. It is rejected on `update()`: what a document settles into is
decided when it is written, not renegotiated afterwards.

Only `sale` produces revenue and therefore a fiscal document. `funding` (share subscriptions, loans,
capital contributions, grants) and `agreement` (NDAs, framework agreements, term sheets) settle
without an invoice being issued. See
[Billing Packages — Outcome Mode](billing-packages.md#outcome-mode) for the full table, and the
`Enlivy\Enums\BillingPackage\OutcomeMode` enum for the values.

`Proposal\Statuses` gained `agreed`, the terminal state for a non-`sale` proposal: it is reached from
`accepted` once no required contract is still outstanding, and it is where such a proposal stops —
there is no payment or invoice for it to progress to. A `sale` proposal never reaches it.

## Stages

`status` records what the proposal row was last set to. `stage` answers the different question of
where the whole thing is sitting, reading across the proposal, its contracts and their parties — so
two proposals that are both `accepted` can report different stages.

| Stage | Meaning |
|-------|---------|
| `drafting` | Not yet sent |
| `awaiting_acceptance` | Sent, no answer yet |
| `awaiting_contract` | Accepted; a required contract has not been generated |
| `awaiting_signature` | Contract generated, signatures outstanding |
| `awaiting_payment` | Signed; payment outstanding |
| `closed` | Finished |
| `rejected` | Declined |
| `expired` | Lapsed |

`closed`, `rejected` and `expired` are terminal — but a proposal closed too early can be put back
in play:

```php
<?php

$proposal = $client->proposals->reopen('org_prop_xxx');
```

Reopening emits the `proposal.reopened` event
(`Enlivy\Enums\EventDelivery\TriggerEvent::PROPOSAL_REOPENED`), so destinations subscribed to the
proposal lane see it as a distinct transition rather than a silent status edit.

`closed`, `rejected` and `expired` are terminal. The `stage_detail` include adds whose move it is and
what is missing:

```php
<?php

$proposal = $client->proposals->retrieve('org_prop_xxx', [
    'include' => ['stage_detail'],
]);

$detail = $proposal->stage_detail;

echo $detail->stage;              // e.g. "awaiting_signature"
echo $detail->awaits;             // organization | customer | third_party | several
var_dump($detail->is_terminal);
var_dump($detail->blockers);            // what stands in the way of generating a contract
var_dump($detail->pending_signatures);  // parties who still owe a signature
```

`blockers` and `pending_signatures` are both empty whenever nothing stands in the way — including at
every terminal stage — so an empty list is the positive signal too, not only the absence of a
negative one. Keep the include off list calls: resolving it walks the whole contract cast per row.

## Settlement Currency

A proposal can be quoted in one currency and settled in another. `allowed_currencies` states which
currencies it will settle in, and `exchange_rate_guarantee` decides when the rate stops moving:

| Guarantee | Rate fixed at |
|-----------|---------------|
| `invoice` | Each invoice is issued — the rate is re-quoted then |
| `acceptance` | Acceptance — the figures the customer accepted are frozen |

```php
<?php

$proposal = $client->proposals->create([
    'currency' => 'EUR',
    'allowed_currencies' => ['EUR', 'RON'],
    'billed_currency' => 'RON',
    'exchange_rate_guarantee' => 'acceptance',
    // ...
]);
```

Both `currency` and `billed_currency` must appear in `allowed_currencies`. Once a contract has been
generated from the proposal, `billed_currency` and `exchange_rate_guarantee` are both locked — the
document already names them.

From the customer-portal lane the customer can re-quote the held rate before answering, and name the
currency they are accepting in:

```php
<?php

use Enlivy\EnlivyPortalClient;

$portal = new EnlivyPortalClient([
    'session_token' => $sessionToken,
    'organization_id' => 'org_xxx',
]);

// Re-quote the held conversion — returns the refreshed figures, untyped
$conversion = $portal->proposals->refreshConversion('org_prop_xxx');

// Accept in a chosen currency, confirming the amount that was displayed
$proposal = $portal->proposals->accept('org_prop_xxx', [
    'billed_currency' => 'RON',
    'displayed_amount' => 4995.00,
]);
```

`displayed_amount` is the figure the customer was shown; sending it lets the API refuse an acceptance
whose numbers have since moved. Read `billed_currency_is_choosable` to know whether to offer the
choice at all.

`billed_conversion` is null until a conversion has actually been applied. When present it carries:

| Field | Type | Meaning |
|-------|------|---------|
| `amount` | string | The converted total, as a decimal string |
| `currency` | string | The currency that amount is in |
| `valid_until` | string\|null | When the held rate stops being honoured |
| `refreshable_at` | string\|null | When `refreshConversion()` will next re-quote |

`amount` is a **decimal string**, not a float — it is money, already rounded to the currency's minor
unit and with the conversion fee applied. Keep it in string or decimal form; casting it to a float to
do arithmetic is how rounding errors get into invoices.

## Notification Logs

Every notice the platform sends about a proposal is recorded — both the proposal that went out to the
customer and the lifecycle reports that came back to the organization.

```php
<?php

$logs = $client->proposalNotificationLogs->list([
    'organization_proposal_id' => 'org_prop_xxx',
    'types' => 'email_seller_accepted,email_seller_rejected',
    'include' => ['proposal'],
]);

foreach ($logs as $log) {
    echo $log->type;
    var_dump($log->is_seller_notification);  // true = reported to the organization
    echo $log->subject;
    echo $log->sent_to;
}

$log = $client->proposalNotificationLogs->retrieve('org_prop_nl_xxx');
$client->proposalNotificationLogs->delete('org_prop_nl_xxx');
$client->proposalNotificationLogs->restore('org_prop_nl_xxx');
```

Read `is_seller_notification` rather than testing the `email_seller_` prefix yourself. Values are in
`Enlivy\Enums\Proposal\NotificationLogTypes`; filters are indexed in
[Filters](../filters.md).

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

- [Billing Packages](billing-packages.md) - Create billing package templates
- [Prospects](prospects.md) - Send proposals to prospects
- [Contracts](contracts.md) - Create contracts after acceptance
- [Invoices](invoices.md) - Invoice based on accepted proposal

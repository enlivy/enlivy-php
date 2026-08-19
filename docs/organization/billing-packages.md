# Billing Packages

Billing packages are reusable templates for creating proposals. They define payment plans, product groups, and optional contract templates that can be offered to prospects and customers.

## Key Concepts

### Structure

```
BillingPackage
    |
    +-- Groups (product groupings)
    |       |
    |       +-- Items (products/services)
    |
    +-- Payment Plans (one_time pricing options)
    |
    +-- Subscription Terms (subscription cadence variants)
    |       |
    |       +-- Items (per-(term, group item) availability + price)
    |
    +-- Contract Templates (optional contracts)
            |
            +-- Sections (contract content)
```

### Types

Billing packages have a `type` field: `one_time` (priced via **Payment Plans**) or
`subscription` (priced via **Subscription Terms** — one or more cadence variants such as
Monthly / Annual, each with its own frequency, currency, and per-item pricing). Exactly one
subscription term is the default a sell flow falls back to when no specific term is chosen.

## Creating a Billing Package

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$package = $client->billingPackages->create([
    'alias' => 'web-design-2026',
    'name_lang_map' => [
        'en' => 'Web Design Package',
        'ro' => 'Pachet Design Web',
    ],
    'description_lang_map' => [
        'en' => 'Complete website design and development package.',
    ],
    'locale' => 'en',
    'locale_list' => ['en', 'ro'],
    'type' => 'standard',
    'is_active' => true,

    // Optional: link to a project
    'organization_project_id' => 'org_proj_xxx',

    // Optional: expiration
    'expires_at' => '2026-12-31',

    // Proposal validity (seconds)
    'proposal_valid_for_seconds' => 604800, // 7 days

    // Allowed payment methods
    'allowed_payment_methods' => ['bank_transfer', 'card'],

    // Product groups
    'groups' => [
        [
            'name_lang_map' => ['en' => 'Design Services'],
            'order' => 1,
            'items' => [
                [
                    'organization_product_id' => 'org_prod_xxx',
                    'quantity' => 1,
                    'order' => 1,
                ],
            ],
        ],
    ],

    // Payment plans
    'payment_plans' => [
        [
            'name_lang_map' => ['en' => 'Monthly Plan'],
            'description_lang_map' => ['en' => 'Pay in 3 monthly installments'],
            'currency' => 'EUR',
            'frequency' => 'monthly',
            'due_date_type' => 'days',
            'due_date_days' => 30,
            'is_active' => true,
            'order' => 1,
        ],
        [
            'name_lang_map' => ['en' => 'One-Time Payment'],
            'description_lang_map' => ['en' => 'Pay upfront with 10% discount'],
            'currency' => 'EUR',
            'frequency' => 'one_time',
            'due_date_type' => 'days',
            'due_date_days' => 0,
            'is_active' => true,
            'order' => 2,
        ],
    ],
]);

echo "Billing package created: {$package->id}\n";
```

## Outcome Mode

`outcome_mode` decides what a proposal built from the package settles into — and therefore whether
money moving ever produces a fiscal document:

| Mode | Settles into | Issues an invoice |
|------|--------------|-------------------|
| `sale` (default) | Revenue | Yes |
| `funding` | Equity or a liability — share subscriptions, convertible loans, capital contributions, grants | No |
| `agreement` | Nothing monetary — NDAs, framework agreements, term sheets | No |

```php
use Enlivy\Enums\BillingPackage\OutcomeMode;

$client->billingPackages->create([
    // ...
    'outcome_mode' => OutcomeMode::FUNDING->value,
]);
```

Only `sale` issues fiscal documents. Money arriving against a `funding` package lands on the balance
sheet and sits outside the scope of VAT, so invoicing it would misstate the books — the charge
pipeline declines to issue rather than issuing a zero-rated document.

A customer deposit is usually an advance against a future supply, which *is* a taxable event: use
`sale` for it. Reserve `funding` for a genuinely refundable deposit never applied to the price.

The mode carries onto the proposal built from the package and is read-only there.

## Quantity Price Tiers

A line the customer chooses a quantity for can be priced on a ladder rather than a flat unit price.
Set the ladder on a group item, on a payment-plan phase line item, or both:

```php
use Enlivy\Enums\BillingPackage\TierPriceType;

'quantity_price_tiers' => [
    'price_type' => TierPriceType::FIXED->value,
    'tiers' => [
        ['min_quantity' => 1,  'price' => 100],
        ['min_quantity' => 10, 'price' => 90],
        ['min_quantity' => 50, 'price' => 75],
    ],
],
```

| `price_type` | Each row carries | Meaning |
|--------------|------------------|---------|
| `fixed` | `price` | The unit price at that quantity, frozen as typed |
| `percent_of_baseline` | `discount_percent` | A discount off the product's catalog price, so the ladder follows the catalog |

Rows must ascend by `min_quantity`, which starts at 1. A row carrying the key the other `price_type`
expects is rejected rather than ignored — a `fixed` ladder may not carry `discount_percent`, and
vice versa. `discount_percent` is capped at 100.

Phase line items additionally declare whether a quantity may be chosen at all:

| Field | Description |
|-------|-------------|
| `allow_quantity` | Whether the customer picks a quantity for this line |
| `min_quantity` | Lowest selectable quantity (at least 1) |
| `max_quantity` | Highest selectable quantity, or `null` for unbounded |
| `quantity_price_tiers` | The ladder above, or `null` for flat pricing |

How the chosen quantities are sent back depends on the package type:

| Package type | Where quantities go |
|--------------|---------------------|
| `one_time` | `line_quantities` — payment-plan phase line items, by id |
| `subscription` | `selected_group_items[].quantity` — group items, alongside the selection itself |

Both are accepted on `proposals->fromBillingPackage()` and on a customer-portal claim. See
[Creating a Proposal from a Billing Package](#creating-a-proposal-from-a-billing-package).

## Subscription Cadence Variants

A `subscription` package carries one or more **subscription terms** (cadence variants), each
priced per group item. Author them inline on the package; on read they come back through the
`subscription_terms` include.

```php
<?php

$package = $client->billingPackages->create([
    'alias' => 'membership-2026',
    'name_lang_map' => ['en' => 'Membership'],
    'type' => 'subscription',
    'is_active' => true,
    'groups' => [ /* product groups + items */ ],
    'subscription_terms' => [
        [
            'name_lang_map' => ['en' => 'Monthly'],
            'primary_currency' => 'EUR',
            'frequency' => 'monthly',
            'is_default' => true,
            'order' => 1,
            'items' => [
                [
                    'organization_billing_package_group_item_id' => 'org_bp_grpi_xxx',
                    'is_available' => true,
                    'prices' => [
                        ['currency' => 'EUR', 'price' => '34.00', 'discount' => '0'],
                    ],
                ],
            ],
        ],
        [
            'name_lang_map' => ['en' => 'Annual'],
            'primary_currency' => 'EUR',
            'frequency' => 'yearly',
            'order' => 2,
            'items' => [
                [
                    'organization_billing_package_group_item_id' => 'org_bp_grpi_xxx',
                    'is_available' => true,
                    'prices' => [
                        ['currency' => 'EUR', 'price' => '340.00', 'discount' => '0'],
                    ],
                ],
            ],
        ],
    ],
]);
```

> **`prices` read vs write shape.** On write, `prices` is a list of `{currency, price,
> discount}` rows (above). On read, a term item's `prices` comes back as a currency-keyed
> map — `['EUR' => ['price' => '34.00', 'discount' => '0']]`. Amounts are decimal strings; a
> currency absent from the map falls back to the product catalog price (or FX from the term's
> `primary_currency`). An item with `is_available => false` is not offered under that cadence.

Frequencies: `weekly`, `biweekly`, `monthly`, `every_3_months`, `every_6_months`, `yearly`.
A term's `status` is `active` (sellable) or `archived` (hidden from public/portal lanes).

## Listing Billing Packages

```php
<?php

// List all active packages
$packages = $client->billingPackages->list([
    'is_active' => true,
    'include' => ['groups', 'payment_plans'],
]);

foreach ($packages as $package) {
    echo "{$package->alias}: " . ($package->name_lang_map['en'] ?? 'Untitled') . "\n";
}

// Filter by project
$projectPackages = $client->billingPackages->list([
    'organization_project_id' => 'org_proj_xxx',
]);

// Only available (active + not expired)
$available = $client->billingPackages->list([
    'only_available' => true,
]);
```

## Retrieving a Billing Package

```php
<?php

$package = $client->billingPackages->retrieve('org_bp_xxx', [
    'include' => ['groups', 'payment_plans', 'contract_templates', 'created_by_user'],
]);

echo "Package: " . ($package->name_lang_map['en'] ?? '') . "\n";
echo "Active: " . ($package->is_active ? 'Yes' : 'No') . "\n";
echo "Available: " . ($package->is_available ? 'Yes' : 'No') . "\n";
```

## Updating a Billing Package

```php
<?php

$package = $client->billingPackages->update('org_bp_xxx', [
    'name_lang_map' => [
        'en' => 'Web Design Package (Updated)',
    ],
    'is_active' => true,
]);
```

## Expiring a Billing Package

Expire a billing package to make it unavailable for new proposals without deleting it:

```php
<?php

$package = $client->billingPackages->expire('org_bp_xxx');

echo "Expired at: {$package->expired_at}\n";
```

## Deleting and Restoring

```php
<?php

// Soft-delete
$package = $client->billingPackages->delete('org_bp_xxx');

// Restore
$package = $client->billingPackages->restore('org_bp_xxx');
```

## Contract Templates

A billing package can carry contract templates, each built from ordered **sections**. A section's
`content_source` selects what fills it, alongside an accompanying `configuration` object:

| `content_source` | Fills the section with |
|------------------|------------------------|
| `standard` | Free-form authored content |
| `reusable_content` | A shared reusable content block |
| `purchase_items` | The purchased line items |
| `purchase_terms` | The subscription / payment terms |
| `purchase_summary` | A totals summary of the purchase |
| `product_list` | The package's product list |
| `purchased_product_list` | Only the products actually purchased |

Values come from the `Enlivy\Enums\BillingPackage\ContractSectionContentSources` enum.

For the product-list sources, `configuration` narrows which products appear:
`{'mode': 'allow'|'exclude', 'product_ids': [...]}`.

Templates also carry per-party identity blocks: `sender_rawd_lang_map` and
`receiver_rawd_lang_map` (localizable raw party details rendered into the
contract header), writable on `contract_templates[]` alongside the sections.

### Naming a template's parties

By default a template has the sender/receiver pairing every document has always had. Set
`party_selection` to `custom` to declare the cast yourself — a three-way agreement, a witness, a
guarantor:

```php
use Enlivy\Enums\BillingPackage\ContractPartySelections;
use Enlivy\Enums\BillingPackage\ContractPartySources;

$client->billingPackages->update('org_bp_xxx', [
    'contract_templates' => [[
        'id' => 'tpl_xxx',
        'party_selection' => ContractPartySelections::CUSTOM->value,
        'parties' => [
            [
                'source' => ContractPartySources::SENDER->value,
                'referenced_as_lang_map' => ['en' => 'Provider'],
                'role_lang_map' => ['en' => 'Service provider'],
                'is_signature_required' => true,
                'order' => 0,
            ],
            [
                'source' => ContractPartySources::RECEIVER->value,
                'referenced_as_lang_map' => ['en' => 'Client'],
                'is_signature_required' => true,
                'order' => 1,
            ],
            [
                'source' => ContractPartySources::ASSIGNED->value,
                'organization_user_id' => 'org_user_xxx',
                'referenced_as_lang_map' => ['en' => 'Guarantor'],
                'is_signature_required' => true,
                'order' => 2,
            ],
        ],
    ]],
]);
```

| `source` | Whose identity fills the party |
|----------|-------------------------------|
| `sender` | The issuing organization's own registered identity |
| `receiver` | Whoever the deal is with; the template names the role, not the person |
| `assigned` | A specific organization user, named by `organization_user_id` |
| `stated` | Nobody on file — the row states the identity itself |

Every source fills the same way: a base identity is resolved, then any detail stated on the row wins
over it. So `stated` is simply the case where the source contributes nothing, and any party may
override individual fields (`first_name`, `organization_name`, `contact_email_address`, the
`address_*` block, and the country's identity fields).

`organization_user_id` is required for `assigned` and rejected for every other source.
`referenced_as_lang_map` is required on every row — it is the name clauses address the party by, and
the merge-field tag is derived from it per locale (read back as `merge_field_alias_lang_map`).

Leaving `party_selection` at `standard` writes nothing to the party table, so the declared cast only
becomes authoritative once you opt in. Send `_deleted => true` on a row to drop it.

Reading the cast back needs no opt-in: `parties` is a **default include** on a contract template, so
it arrives with the template whenever you retrieve the package with `contract_templates`. It is also
listed as an available include, so it survives an explicit `include` list — but you do not have to
ask for it.

The customer-portal lane deliberately omits it. A template served through
`$portal->billingPackages` carries its `sections` but never its `parties`: the declared cast names
the contacts an organization pins to roles in its own documents, which is authoring state rather than
something a customer browsing the catalog is answered with. Do not expect the key there.

Rules that need the whole merged row — one sending party, one receiving party, a `stated` party
complete enough to be named and invited — are checked on save rather than per field, so a partial
resubmission is judged on what the template will actually hold.

### Previewing a Contract Template

Render a preview of one of the package's contract templates. The optional `locale` must be one of
the package's locales. The result is a rendered `Contract` preview and is not persisted:

```php
<?php

$contract = $client->billingPackages->previewContractTemplate('org_bp_xxx', 'tpl_xxx', [
    'locale' => 'en',
]);
```

## Creating a Proposal from a Billing Package

Use the proposals service to create a proposal directly from a billing package:

```php
<?php

$proposal = $client->proposals->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    // one_time packages: pick a payment plan
    'organization_billing_package_payment_plan_id' => 'org_bp_plan_xxx',
    // subscription packages: optionally pin a cadence variant (omit = the package default)
    'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',

    // Recipient (choose one)
    'organization_prospect_id' => 'org_pros_xxx',
    // OR: 'organization_receiver_user_id' => 'org_user_xxx',
    // OR: 'recipient_email' => 'client@example.com', 'recipient_name' => 'John Doe',

    // Optional sender
    'organization_sender_user_id' => 'org_user_xxx',

    // Quantities for lines that allow one (see Quantity Price Tiers)
    'line_quantities' => [
        ['id' => 'org_bp_pppli_xxx', 'quantity' => 12],
    ],
]);

echo "Proposal created: {$proposal->id}\n";
```

`line_quantities` names payment-plan phase line items by id and is honoured only for lines whose
`allow_quantity` is true. Each `quantity` is at least 1, ids must be distinct, and the line must
belong to this organization. Omit a line and it takes its own `min_quantity`.

It applies to **one-time packages only**. A subscription package carries its quantities on
`selected_group_items[].quantity` instead — same idea, different level of the tree, because a
subscription's customer picks group items rather than plan lines. The same keys are accepted on a
customer-portal claim.

## Creating a Billing Schedule from a Billing Package

A subscription package becomes recurring billing in one of two ways:

- **Via a proposal** (above) — you quote the customer, they accept and pay, and the
  billing schedule activates on that first payment.
- **Directly** — when you already have the customer and their payment method and just
  want to start billing. `billingSchedules->fromBillingPackage()` materializes the
  subscription schedule straight from the package, and can charge the first cycle inline.

```php
<?php

$schedule = $client->billingSchedules->fromBillingPackage([
    'organization_billing_package_id' => 'org_bp_xxx',
    // Optional cadence variant (omit = the package default)
    'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',
    // Optional composition (quantities / add-ons); omit to use the package defaults
    'selected_group_items' => [
        ['id' => 'org_bp_grpi_xxx', 'quantity' => 2],
    ],

    'organization_sender_user_id' => 'org_user_sender',
    'organization_receiver_user_id' => 'org_user_customer',
    'organization_user_payment_method_id' => 'org_user_pm_xxx',

    // pending = set up now, activate on the first payment; active = start billing
    'status' => 'active',

    // When the first cycle starts, anchoring the schedule. Omit or null = start now.
    'start_at' => null,
]);

echo "Schedule {$schedule->id} ({$schedule->status})\n";
```

The package owns the composition, so `phases`, `payments`, `direction` and
`management_type` are derived from it and are rejected if you send them.

### Immediate first charge

If the schedule is created `active` and its first cycle is already due (for example
`start_at` is null/now), that cycle is invoiced and charged inline against the receiver's
saved payment method. The created schedule is returned as usual; the charge outcome rides
on the response `meta`, reachable through `lastResponse()`:

```php
<?php

$schedule = $client->billingSchedules->fromBillingPackage([/* … */]);

$meta      = $schedule->lastResponse()?->json['meta'] ?? [];
$charge    = $meta['charge_result'] ?? null;   // null when nothing was billed
$invoiceId = $meta['invoice_id'] ?? null;      // the invoice the first cycle generated

if ($charge !== null && $charge['status'] === 'requires_action') {
    // Card needs 3DS/SCA — send the customer to complete it:
    $redirectTo = $charge['next_action_url'];
}
```

`charge_result` fields:

| Field | Meaning |
|-------|---------|
| `status` | `succeeded` (charged) · `requires_action` (needs 3DS/SCA — see `next_action_url`) · `failed` (declined; the invoice stays open and the cron retries) · `already_paid` |
| `error_code` | Stable machine code (e.g. `card_declined`) to localize on the frontend |
| `error_message` | Raw provider message (audit detail) |
| `provider_reference` | Payment-provider reference for the attempt |
| `next_action_url` | Authentication URL when `status` is `requires_action`; null otherwise |

`charge_result` is `null` when nothing was billed — the schedule isn't `active`, it starts
in the future, or the organization's billing-schedules feature is inactive. A non-card
payment method still generates the invoice (collect it manually) and reports `status`
`failed` with `error_code` `payment_method_not_auto_chargeable`.

> The same `meta.charge_result` / `meta.invoice_id` rides on `billingSchedules->create()`
> (raw phases/payments) when a schedule is created `active` and due — read it the same way.

### When the invoice is issued

A schedule chooses when each cycle's invoice gets its number:

```php
use Enlivy\Enums\BillingSchedule\InvoiceIssueTrigger;

$client->billingSchedules->update('org_bs_xxx', [
    'invoice_issue_trigger' => InvoiceIssueTrigger::ON_PAYMENT->value,
]);
```

| Trigger | Behaviour |
|---------|-----------|
| `on_generation` | The invoice is issued when the cycle generates it |
| `on_payment` | The invoice is generated but stays unissued until the cycle is actually paid |

Invoice numbers come from a gapless counter allocated the moment an invoice leaves its pre-live
statuses, so `on_generation` burns a number on every cycle including the ones that fail to collect.
Pick `on_payment` where an unused number in the sequence would be a problem.

The field is writable on both `create()` and `update()`, and reads back on the schedule.

> **Package fields are not accepted on `create()`.** The standard create endpoint composes
> explicit `phases`/`payments` only; `organization_billing_package_id`,
> `organization_billing_package_subscription_term_id`, `selected_group_items` and `start_at`
> are rejected there. Use `fromBillingPackage()`. (Earlier SDK versions accepted a package on
> `create()` — see [UPGRADING](../../UPGRADING.md).)

## Client Portal: Billing Packages

Customers can browse and claim billing packages through the Client Portal:

```php
<?php

use Enlivy\EnlivyPortalClient;

$portal = new EnlivyPortalClient([
    'portal_token' => 'session-uuid-token',
    'organization_id' => 'org_xxx',
]);

// List available packages
$packages = $portal->billingPackages->list();

// View package details
$package = $portal->billingPackages->retrieve('org_bp_xxx');

// Claim a package (creates an accepted proposal)
$result = $portal->billingPackages->claim('org_bp_xxx', [
    // subscription packages: the chosen cadence variant (omit = the package default)
    'organization_billing_package_subscription_term_id' => 'org_bp_st_xxx',
    // one_time packages: the chosen payment plan
    // 'organization_billing_package_payment_plan_id' => 'org_bp_plan_xxx',
]);
```

### Managing a Subscription

Claiming a subscription package creates a **billing schedule**. The customer manages it
through the portal:

```php
<?php

// Preview a composition change (quantities / add-ons) before applying — returns the
// proration + next-cycle estimate, not the schedule.
$preview = $portal->billingSchedules->previewReconfigure('org_bs_xxx', [
    'selected_group_items' => [
        ['id' => 'org_bp_grpi_xxx', 'quantity' => 2],
    ],
]);

// Apply the change
$schedule = $portal->billingSchedules->reconfigure('org_bs_xxx', [
    'selected_group_items' => [
        ['id' => 'org_bp_grpi_xxx', 'quantity' => 2],
    ],
]);

$portal->billingSchedules->pause('org_bs_xxx');
$portal->billingSchedules->resume('org_bs_xxx');
$portal->billingSchedules->cancel('org_bs_xxx', ['reason' => 'No longer needed']);
$portal->billingSchedules->changePaymentMethod('org_bs_xxx', [
    'organization_user_payment_method_id' => 'org_user_pm_xxx',
]);
```

The schedule's `customer_can_reconfigure` / `customer_can_pause` / `customer_can_cancel` flags
indicate which self-service actions are available. Portal reconfigure changes the package
composition (quantities / add-ons) within the current cadence; switching to a different cadence
variant is an admin operation (`$client->billingSchedules->reconfigure(...)` with an
`organization_billing_package_subscription_term_id`).

## Available Includes

| Include | Description |
|---------|-------------|
| `organization` | Parent organization |
| `project` | Linked project |
| `groups` | Product groups with items |
| `payment_plans` | Available payment plans (one_time) |
| `subscription_terms` | Subscription cadence variants with their per-item pricing |
| `contract_templates` | Attached contract templates |
| `created_by_user` | User who created the package |
| `deleted_by_user` | User who deleted the package |
| `expired_by_user` | User who expired the package |

## Available Filters

| Filter | Type | Description |
|--------|------|-------------|
| `is_active` | bool | Filter by active status |
| `type` | string | Filter by package type |
| `organization_project_id` | string | Filter by project |
| `only_available` | bool | Only active and non-expired |

## Related

- [Proposals](proposals.md) - Create proposals from billing packages
- [Products](products.md) - Products used in package groups
- [Contracts](contracts.md) - Contract templates in packages

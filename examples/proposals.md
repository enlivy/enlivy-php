# Proposals

Send professional proposals to prospects using offer templates.

## Concepts

- **Offer**: A template defining pricing, payment plans, and terms
- **Proposal**: An instance of an offer sent to a specific prospect

## Creating an Offer Template

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$offer = $client->offers->create([
    'name' => 'Web Development Package',
    'description' => 'Complete website development service',
    'currency' => 'EUR',
    'is_active' => true,
    'line_items' => [
        [
            'name' => 'Design Phase',
            'description' => 'UX/UI design and prototyping',
            'quantity' => 1,
            'price' => 3000.00,
            'type' => 'service',
        ],
        [
            'name' => 'Development',
            'description' => 'Frontend and backend development',
            'quantity' => 1,
            'price' => 7000.00,
            'type' => 'service',
        ],
        [
            'name' => 'Testing & Launch',
            'description' => 'QA testing and deployment',
            'quantity' => 1,
            'price' => 2000.00,
            'type' => 'service',
        ],
    ],
    'payment_plans' => [
        [
            'name' => 'Full Payment',
            'description' => '5% discount for full payment',
            'discount_percent' => 5,
            'installments' => 1,
        ],
        [
            'name' => '3 Installments',
            'description' => 'Pay in 3 monthly installments',
            'discount_percent' => 0,
            'installments' => 3,
        ],
    ],
]);

echo "Offer created: {$offer->id}\n";
```

## Listing Offers

```php
<?php

$offers = $client->offers->list([
    'filter' => ['is_active' => true],
]);

foreach ($offers as $offer) {
    echo "{$offer->name} - {$offer->currency}\n";
}
```

## Creating a Proposal

Send an offer to a specific prospect:

```php
<?php

$proposal = $client->proposals->create([
    'organization_offer_id' => 'org_offr_xxx',
    'organization_prospect_id' => 'org_pros_xxx',
    'recipient_email' => 'prospect@example.com',
    'recipient_name' => 'John Doe',
    'valid_until' => '2026-03-05',
    'message' => 'Please find our proposal for your website project.',
    'selected_payment_plan' => 0, // Index of payment plan
]);

echo "Proposal created: {$proposal->id}\n";
echo "View URL: {$proposal->view_url}\n";
```

## Listing Proposals

```php
<?php

$proposals = $client->proposals->list([
    'include' => ['offer', 'prospect'],
]);

foreach ($proposals as $proposal) {
    $status = $proposal->accepted_at ? 'Accepted' : ($proposal->declined_at ? 'Declined' : 'Pending');
    echo "{$proposal->offer->name} to {$proposal->recipient_name} - {$status}\n";
}
```

## Checking Proposal Status

```php
<?php

$proposal = $client->proposals->retrieve('org_prop_xxx');

if ($proposal->accepted_at) {
    echo "Accepted on {$proposal->accepted_at}\n";
    echo "Selected plan: {$proposal->selected_payment_plan}\n";
} elseif ($proposal->declined_at) {
    echo "Declined on {$proposal->declined_at}\n";
} else {
    echo "Pending - Valid until {$proposal->valid_until}\n";
}
```

## Complete Workflow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

// 1. Create offer template (once)
$offer = $client->offers->create([
    'name' => 'Consulting Retainer',
    'currency' => 'EUR',
    'line_items' => [
        ['name' => 'Monthly Retainer', 'quantity' => 1, 'price' => 5000.00, 'type' => 'service'],
    ],
    'payment_plans' => [
        ['name' => 'Monthly', 'installments' => 1],
        ['name' => 'Quarterly', 'installments' => 1, 'discount_percent' => 10],
    ],
]);

// 2. When prospect is qualified, send proposal
$prospect = $client->prospects->retrieve('org_pros_xxx');

$proposal = $client->proposals->create([
    'organization_offer_id' => $offer->id,
    'organization_prospect_id' => $prospect->id,
    'recipient_email' => $prospect->email,
    'recipient_name' => "{$prospect->first_name} {$prospect->last_name}",
    'valid_until' => date('Y-m-d', strtotime('+14 days')),
    'message' => "Hi {$prospect->first_name}, here's our proposal.",
]);

// 3. Log activity
$client->prospectActivities->create([
    'organization_prospect_id' => $prospect->id,
    'type' => 'email',
    'title' => 'Proposal sent',
    'description' => "Sent proposal for {$offer->name}",
]);

// 4. Advance prospect
$client->prospects->advance($prospect->id, [
    'note' => 'Proposal sent',
]);

echo "Proposal sent: {$proposal->view_url}\n";
```

## Related

- [Prospects](prospects.md) - Send proposals to prospects
- [Contracts](contracts.md) - Create contracts after acceptance
- [Invoices](invoices.md) - Invoice based on accepted proposal

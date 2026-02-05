# Contracts

Create, manage, and get e-signatures on contracts.

## Prerequisites

Before creating a contract, you need:
1. An **OrganizationUser** (the party signing the contract)
2. Contract statuses configured (optional, for workflow)

See [Organization Users](organization-users.md) to create parties first.

## Creating a Contract

### Basic Contract

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$contract = $client->contracts->create([
    'title' => 'Service Agreement',
    'organization_user_id' => 'org_user_xxx', // The other party
    'content_html' => '<h1>Service Agreement</h1><p>Terms and conditions...</p>',
    'organization_contract_status_id' => 'org_cont_status_xxx', // Draft status
]);

echo "Contract created: {$contract->id}\n";
echo "Title: {$contract->title}\n";
```

### Contract with Rich Content

```php
<?php

$contractHtml = <<<HTML
<h1>Service Agreement</h1>

<p><strong>Effective Date:</strong> February 5, 2026</p>

<h2>1. Services</h2>
<p>The Provider agrees to deliver the following services:</p>
<ul>
    <li>Web development and maintenance</li>
    <li>Technical support (business hours)</li>
    <li>Monthly reporting</li>
</ul>

<h2>2. Payment Terms</h2>
<p>Client agrees to pay €5,000 per month, due on the 1st of each month.</p>

<h2>3. Term</h2>
<p>This agreement is effective for 12 months from the effective date.</p>

<h2>4. Signatures</h2>
<p>By signing below, both parties agree to the terms above.</p>
HTML;

$contract = $client->contracts->create([
    'title' => 'Service Agreement 2026',
    'organization_user_id' => 'org_user_xxx',
    'content_html' => $contractHtml,
    'organization_contract_status_id' => 'org_cont_status_draft_xxx',

    // Optional metadata
    'start_date' => '2026-02-05',
    'end_date' => '2027-02-04',
    'value' => 60000.00,
    'value_currency' => 'EUR',
]);
```

### Contract with Numbering Prefix

```php
<?php

$contract = $client->contracts->create([
    'title' => 'NDA Agreement',
    'organization_user_id' => 'org_user_xxx',
    'organization_contract_prefix_id' => 'org_cont_prefix_xxx', // e.g., "NDA-2026-"
    'content_html' => '<h1>Non-Disclosure Agreement</h1>...',
]);

echo "Contract number: {$contract->number}\n"; // e.g., "NDA-2026-0001"
```

## Listing Contracts

### Basic List

```php
<?php

$contracts = $client->contracts->list();

foreach ($contracts as $contract) {
    echo "{$contract->number}: {$contract->title} ({$contract->status->name})\n";
}
```

### With Filters

```php
<?php

// Active contracts
$active = $client->contracts->list([
    'filter' => [
        'status.name' => 'Active',
    ],
]);

// Contracts for specific party
$partyContracts = $client->contracts->list([
    'filter' => [
        'organization_user_id' => 'org_user_xxx',
    ],
]);

// Contracts expiring soon
$expiringSoon = $client->contracts->list([
    'filter' => [
        'end_date_to' => date('Y-m-d', strtotime('+30 days')),
    ],
]);
```

### With Related Data

```php
<?php

$contracts = $client->contracts->list([
    'include' => ['user', 'status', 'signatures', 'prefix'],
]);

foreach ($contracts as $contract) {
    $partyName = $contract->user->name
        ?? "{$contract->user->first_name} {$contract->user->last_name}";

    echo "{$contract->title} with {$partyName}\n";
    echo "Status: {$contract->status->name}\n";

    if ($contract->signatures) {
        foreach ($contract->signatures as $sig) {
            $status = $sig->signed_at ? 'Signed' : 'Pending';
            echo "  Signature: {$sig->signer_name} - {$status}\n";
        }
    }
}
```

## Retrieving a Contract

```php
<?php

$contract = $client->contracts->retrieve('org_cont_xxx', [
    'include' => ['user', 'status', 'signatures'],
]);

echo "Contract: {$contract->title}\n";
echo "Number: {$contract->number}\n";
echo "Party: {$contract->user->email}\n";
echo "Status: {$contract->status->name}\n";
echo "Value: {$contract->value} {$contract->value_currency}\n";
```

## Updating a Contract

```php
<?php

$contract = $client->contracts->update('org_cont_xxx', [
    'title' => 'Updated Service Agreement',
    'content_html' => '<h1>Updated Agreement</h1>...',
    'organization_contract_status_id' => 'org_cont_status_review_xxx',
]);
```

## Contract Statuses

### List Statuses

```php
<?php

$statuses = $client->contractStatuses->list();

foreach ($statuses as $status) {
    echo "{$status->name} (order: {$status->order})\n";
}
```

### Create Custom Status

```php
<?php

$status = $client->contractStatuses->create([
    'name' => 'Under Review',
    'color' => '#FFA500', // Orange
    'order' => 2,
    'is_default' => false,
]);
```

### Change Contract Status

```php
<?php

$contract = $client->contracts->update('org_cont_xxx', [
    'organization_contract_status_id' => 'org_cont_status_active_xxx',
]);

echo "New status: {$contract->status->name}\n";
```

## E-Signatures

### Request Signature

```php
<?php

$signature = $client->contractSignatures->create([
    'organization_contract_id' => 'org_cont_xxx',
    'signer_name' => 'John Doe',
    'signer_email' => 'john@example.com',
    'signer_role' => 'Client',
    'order' => 1, // Signing order (for sequential signing)
]);

echo "Signature request created: {$signature->id}\n";
echo "Status: Pending\n";
```

### Request Multiple Signatures

```php
<?php

// Client signature first
$clientSig = $client->contractSignatures->create([
    'organization_contract_id' => 'org_cont_xxx',
    'signer_name' => 'John Doe',
    'signer_email' => 'john@example.com',
    'signer_role' => 'Client',
    'order' => 1,
]);

// Then company representative
$companySig = $client->contractSignatures->create([
    'organization_contract_id' => 'org_cont_xxx',
    'signer_name' => 'Jane Smith',
    'signer_email' => 'jane@mycompany.com',
    'signer_role' => 'Account Manager',
    'order' => 2,
]);
```

### List Signatures for Contract

```php
<?php

$signatures = $client->contractSignatures->list([
    'filter' => [
        'organization_contract_id' => 'org_cont_xxx',
    ],
]);

foreach ($signatures as $sig) {
    $status = $sig->signed_at ? "Signed on {$sig->signed_at}" : 'Pending';
    echo "{$sig->signer_name} ({$sig->signer_role}): {$status}\n";
}
```

### Check Signature Status

```php
<?php

$signature = $client->contractSignatures->retrieve('org_cont_sig_xxx');

if ($signature->signed_at) {
    echo "Signed by {$signature->signer_name} on {$signature->signed_at}\n";
    echo "IP: {$signature->signed_ip}\n";
} else {
    echo "Awaiting signature from {$signature->signer_email}\n";
}
```

## Downloading Contract PDF

```php
<?php

// Download as PDF
$pdf = $client->contracts->download('org_cont_xxx');

// Save to file
file_put_contents('contract.pdf', $pdf);

// Or with signatures overlay
$pdf = $client->contracts->download('org_cont_xxx', [
    'include_signatures' => true,
]);
```

## Contract Prefixes

### List Prefixes

```php
<?php

$prefixes = $client->contractPrefixes->list();

foreach ($prefixes as $prefix) {
    echo "{$prefix->name}: {$prefix->prefix} (next: {$prefix->next_number})\n";
}
```

### Create Prefix

```php
<?php

$prefix = $client->contractPrefixes->create([
    'name' => 'NDA Contracts',
    'prefix' => 'NDA',
    'next_number' => 1,
    'padding' => 4, // NDA-0001
]);
```

## Deleting a Contract

```php
<?php

$contract = $client->contracts->delete('org_cont_xxx');

echo "Contract deleted at: {$contract->deleted_at}\n";
```

## Restoring a Contract

```php
<?php

$contract = $client->contracts->restore('org_cont_xxx');

echo "Contract restored: {$contract->title}\n";
```

## Complete Example: Contract Workflow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Get or create statuses
    $statuses = $client->contractStatuses->list();
    $draftStatus = null;
    $activeStatus = null;

    foreach ($statuses as $status) {
        if ($status->name === 'Draft') $draftStatus = $status;
        if ($status->name === 'Active') $activeStatus = $status;
    }

    // 2. Create the contract
    $contract = $client->contracts->create([
        'title' => 'Software Development Agreement',
        'organization_user_id' => 'org_user_client_xxx',
        'organization_contract_status_id' => $draftStatus->id,
        'start_date' => '2026-03-01',
        'end_date' => '2027-02-28',
        'value' => 50000.00,
        'value_currency' => 'EUR',
        'content_html' => <<<HTML
        <h1>Software Development Agreement</h1>
        <p>This agreement is entered into between...</p>
        <!-- Full contract content -->
        HTML,
    ]);

    echo "Contract created: {$contract->id}\n";

    // 3. Request signatures
    $clientSignature = $client->contractSignatures->create([
        'organization_contract_id' => $contract->id,
        'signer_name' => 'Client Representative',
        'signer_email' => 'client@example.com',
        'signer_role' => 'Client',
        'order' => 1,
    ]);

    $ourSignature = $client->contractSignatures->create([
        'organization_contract_id' => $contract->id,
        'signer_name' => 'Our CEO',
        'signer_email' => 'ceo@ourcompany.com',
        'signer_role' => 'Provider',
        'order' => 2,
    ]);

    echo "Signature requests sent!\n";

    // 4. Download PDF for review
    $pdf = $client->contracts->download($contract->id);
    file_put_contents("contract-{$contract->number}.pdf", $pdf);

    echo "Contract PDF saved\n";

    // 5. Later: Check signature status and activate
    $signatures = $client->contractSignatures->list([
        'filter' => ['organization_contract_id' => $contract->id],
    ]);

    $allSigned = true;
    foreach ($signatures as $sig) {
        if (!$sig->signed_at) {
            $allSigned = false;
            break;
        }
    }

    if ($allSigned) {
        $contract = $client->contracts->update($contract->id, [
            'organization_contract_status_id' => $activeStatus->id,
        ]);
        echo "Contract is now active!\n";
    }

} catch (ValidationException $e) {
    echo "Error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Related

- [Organization Users](organization-users.md) - Create contract parties
- [Proposals](proposals.md) - Create proposals before contracts
- [Invoices](invoices.md) - Invoice based on contract terms

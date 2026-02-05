# Contracts

Create, manage, and get e-signatures on contracts.

## Prerequisites

Before creating a contract, you need:
1. **OrganizationUsers** for both sender and receiver parties
2. **Contract status** configured in your organization
3. Optionally, a **contract prefix** for numbering

See [Organization Users](organization-users.md) to create parties first.

## Contract Concepts

### Direction

| Direction | Description |
|-----------|-------------|
| `inbound` | Contract received from external party |
| `outbound` | Contract you send to external party |

### Source

| Source | Description |
|--------|-------------|
| `internal` | Contract generated in Enlivy |
| `uploaded` | Contract uploaded from external source |

### Category

| Category | Description |
|----------|-------------|
| `core` | Main/primary contract |
| `amendment` | Modification to existing contract (requires parent) |
| `addenda` | Addition to existing contract (requires parent) |
| `supplement` | Supplementary agreement (requires parent) |

## Creating a Contract

### Basic Contract (Uploaded/External)

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$contract = $client->contracts->create([
    // Required fields
    'title' => 'Service Agreement',
    'organization_sender_user_id' => 'org_user_sender_xxx',    // Your company
    'organization_receiver_user_id' => 'org_user_receiver_xxx', // Client
    'organization_contract_status_id' => 'org_cont_status_draft_xxx',
    'category' => 'core',
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',

    // Required for uploaded contracts
    'source' => 'uploaded',
    'number' => 'CONTRACT-2026-001',

    // Optional dates
    'ends_at' => '2027-02-04',
    'renewed_at' => null,
]);

echo "Contract created: {$contract->id}\n";
echo "Number: {$contract->number}\n";
```

### Internal Contract with Content

```php
<?php

$contract = $client->contracts->create([
    // Required fields
    'title' => 'Software Development Agreement',
    'sub_title' => 'For Website Redesign Project',
    'organization_sender_user_id' => 'org_user_company_xxx',
    'organization_receiver_user_id' => 'org_user_client_xxx',
    'organization_contract_status_id' => 'org_cont_status_draft_xxx',
    'category' => 'core',
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',

    // Source
    'source' => 'internal',

    // For internal core contracts, prefix is required
    'organization_contract_prefix_id' => 'org_cont_prefix_xxx',

    // Optional layout (template style)
    'layout' => 'default',

    // Contract dates
    'ends_at' => '2027-02-04',

    // Introduction section
    'content_introduction' => 'This Agreement is entered into as of the date last signed below.',

    // Signature disclaimer
    'content_signature_disclaimer' => 'By signing below, both parties agree to be bound by the terms of this Agreement.',

    // Contract chapters/sections
    'chapters' => [
        [
            'title' => 'Scope of Work',
            'content' => '<p>The Provider agrees to deliver the following services:</p><ul><li>Web design and development</li><li>Quality assurance testing</li><li>Deployment and launch support</li></ul>',
            'order' => 1,
        ],
        [
            'title' => 'Payment Terms',
            'content' => '<p>The Client agrees to pay a total of EUR 50,000, payable as follows:</p><ul><li>30% upon signing</li><li>40% upon design approval</li><li>30% upon project completion</li></ul>',
            'order' => 2,
        ],
        [
            'title' => 'Timeline',
            'content' => '<p>The project shall be completed within 6 months from the effective date.</p>',
            'order' => 3,
        ],
        [
            'title' => 'Confidentiality',
            'content' => '<p>Both parties agree to maintain confidentiality of all proprietary information exchanged during the project.</p>',
            'order' => 4,
        ],
    ],

    // Contract parties with signing requirements
    'parties' => [
        [
            'organization_user_id' => 'org_user_company_xxx',
            'party_type' => 'organization', // organization or individual
            'party_country_code' => 'RO',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'role_in_organization' => 'CEO',
            'organization_name' => 'Tech Solutions SRL',
            'organization_type' => 'SRL',
            'organization_information' => [
                'cui' => '12345678',
                'registration_number' => 'J40/1234/2020',
            ],
            'contact_email_address' => 'john@techsolutions.ro',
            'appears_as_party' => true,
            'is_signature_required' => true,
            'order' => 1,
            'referenced_as_within_document' => 'Provider',
        ],
        [
            'organization_user_id' => 'org_user_client_xxx',
            'party_type' => 'organization',
            'party_country_code' => 'US',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'role_in_organization' => 'CTO',
            'organization_name' => 'Client Corp',
            'organization_type' => 'LLC',
            'contact_email_address' => 'jane@clientcorp.com',
            'contact_phone_number' => '555123456',
            'contact_phone_number_country_code' => 'US',
            'appears_as_party' => true,
            'is_signature_required' => true,
            'order' => 2,
            'referenced_as_within_document' => 'Client',
        ],
    ],
]);

echo "Contract created: {$contract->id}\n";
echo "Number: {$contract->number}\n";
```

### Contract with Individual Party

```php
<?php

$contract = $client->contracts->create([
    'title' => 'Consulting Agreement',
    'organization_sender_user_id' => 'org_user_company_xxx',
    'organization_receiver_user_id' => 'org_user_freelancer_xxx',
    'organization_contract_status_id' => 'org_cont_status_draft_xxx',
    'category' => 'core',
    'direction' => 'outbound',
    'issued_at' => '2026-02-05',
    'source' => 'internal',
    'organization_contract_prefix_id' => 'org_cont_prefix_xxx',

    'chapters' => [
        [
            'title' => 'Services',
            'content' => '<p>Consultant will provide marketing consulting services.</p>',
            'order' => 1,
        ],
    ],

    'parties' => [
        [
            'party_type' => 'organization',
            'party_country_code' => 'RO',
            'first_name' => 'Company',
            'last_name' => 'Representative',
            'organization_name' => 'Company SRL',
            'organization_type' => 'SRL',
            'appears_as_party' => true,
            'is_signature_required' => true,
            'order' => 1,
            'referenced_as_within_document' => 'Company',
        ],
        [
            // Individual party (not organization)
            'party_type' => 'individual',
            'party_country_code' => 'US',
            'first_name' => 'Michael',
            'last_name' => 'Johnson',
            'contact_email_address' => 'michael@consultant.com',
            'information' => [
                'ssn_last_four' => '1234', // Personal info for individual
            ],
            'appears_as_party' => true,
            'is_signature_required' => true,
            'order' => 2,
            'referenced_as_within_document' => 'Consultant',
        ],
    ],
]);
```

### Amendment to Existing Contract

```php
<?php

$amendment = $client->contracts->create([
    'title' => 'Amendment No. 1 to Service Agreement',
    'organization_sender_user_id' => 'org_user_company_xxx',
    'organization_receiver_user_id' => 'org_user_client_xxx',
    'organization_contract_status_id' => 'org_cont_status_draft_xxx',

    // Reference parent contract
    'organization_contract_id' => 'org_cont_parent_xxx', // Parent must be category: core
    'category' => 'amendment', // amendment, addenda, or supplement

    'direction' => 'outbound',
    'issued_at' => '2026-06-01',
    'source' => 'uploaded',
    'number' => 'AMEND-001',

    'chapters' => [
        [
            'title' => 'Amendment',
            'content' => '<p>Section 2 (Payment Terms) is hereby amended to extend payment deadline by 30 days.</p>',
            'order' => 1,
        ],
    ],

    'parties' => [
        // Same parties as parent contract
    ],
]);
```

### Pre-signed Uploaded Contract

```php
<?php

$contract = $client->contracts->create([
    'title' => 'Executed NDA',
    'organization_sender_user_id' => 'org_user_xxx',
    'organization_receiver_user_id' => 'org_user_yyy',
    'organization_contract_status_id' => 'org_cont_status_active_xxx',
    'category' => 'core',
    'direction' => 'inbound',
    'issued_at' => '2026-01-15',
    'source' => 'uploaded',
    'number' => 'NDA-EXT-2026-001',

    // Already signed
    'signed_by_all_parties_at' => '2026-01-20',

    // Attach file if needed
    'organization_file_id' => 'org_file_xxx',
]);
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
        'organization_contract_status_id' => 'org_cont_status_active_xxx',
    ],
]);

// Contracts for specific party
$partyContracts = $client->contracts->list([
    'filter' => [
        'organization_receiver_user_id' => 'org_user_xxx',
    ],
]);

// Contracts by category
$coreContracts = $client->contracts->list([
    'filter' => [
        'category' => 'core',
    ],
]);

// Contracts expiring soon
$expiringSoon = $client->contracts->list([
    'filter' => [
        'ends_at_to' => date('Y-m-d', strtotime('+30 days')),
    ],
]);

// Outbound contracts
$outbound = $client->contracts->list([
    'filter' => [
        'direction' => 'outbound',
    ],
]);
```

### With Related Data

```php
<?php

$contracts = $client->contracts->list([
    'include' => ['sender_user', 'receiver_user', 'status', 'signatures', 'prefix', 'chapters', 'parties'],
]);

foreach ($contracts as $contract) {
    $senderName = $contract->sender_user->name
        ?? "{$contract->sender_user->first_name} {$contract->sender_user->last_name}";
    $receiverName = $contract->receiver_user->name
        ?? "{$contract->receiver_user->first_name} {$contract->receiver_user->last_name}";

    echo "{$contract->title}\n";
    echo "  From: {$senderName}\n";
    echo "  To: {$receiverName}\n";
    echo "  Status: {$contract->status->name}\n";

    if ($contract->parties) {
        foreach ($contract->parties as $party) {
            $signed = $party->is_signed ? 'Signed' : 'Pending';
            echo "  Party: {$party->first_name} {$party->last_name} - {$signed}\n";
        }
    }
}
```

## Retrieving a Contract

```php
<?php

$contract = $client->contracts->retrieve('org_cont_xxx', [
    'include' => ['sender_user', 'receiver_user', 'status', 'signatures', 'chapters', 'parties'],
]);

echo "Contract: {$contract->title}\n";
echo "Number: {$contract->number}\n";
echo "Category: {$contract->category}\n";
echo "Direction: {$contract->direction}\n";
echo "Source: {$contract->source}\n";
echo "Status: {$contract->status->name}\n";
echo "Issued: {$contract->issued_at}\n";

if ($contract->ends_at) {
    echo "Ends: {$contract->ends_at}\n";
}

if ($contract->signed_by_all_parties_at) {
    echo "Fully signed: {$contract->signed_by_all_parties_at}\n";
}
```

## Updating a Contract

```php
<?php

$contract = $client->contracts->update('org_cont_xxx', [
    'title' => 'Updated Service Agreement',
    'ends_at' => '2028-02-04',
    'organization_contract_status_id' => 'org_cont_status_review_xxx',
]);
```

### Update Chapters

```php
<?php

$contract = $client->contracts->update('org_cont_xxx', [
    'chapters' => [
        [
            'id' => 'existing_chapter_id', // Update existing
            'content' => '<p>Updated content for this section.</p>',
        ],
        [
            // Add new chapter
            'title' => 'New Section',
            'content' => '<p>New content.</p>',
            'order' => 5,
        ],
    ],
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
    'organization_user_id' => 'org_user_signer_xxx', // Optional link to org user
    'signer_name' => 'John Doe',
    'signer_email' => 'john@example.com',
    'signer_role' => 'Client Representative',
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
    echo "{$prefix->name}: {$prefix->alias} (next: {$prefix->next_number})\n";
}
```

### Create Prefix

```php
<?php

$prefix = $client->contractPrefixes->create([
    'name' => 'NDA Contracts',
    'alias' => 'NDA',
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

## Field Reference

### Required Fields

| Field | Description |
|-------|-------------|
| `title` | Contract title |
| `organization_sender_user_id` | Sender party user ID |
| `organization_receiver_user_id` | Receiver party user ID |
| `organization_contract_status_id` | Status ID |
| `category` | core, amendment, addenda, supplement |
| `direction` | inbound or outbound |
| `issued_at` | Issue date |

### Conditional Required Fields

| Field | Condition |
|-------|-----------|
| `number` | Required for `source: uploaded` |
| `organization_contract_prefix_id` | Required for `source: internal` + `category: core` |
| `organization_contract_id` | Required for amendment, addenda, supplement categories |

### Party Fields

| Field | Description |
|-------|-------------|
| `party_type` | `organization` or `individual` (required) |
| `party_country_code` | Country code (required) |
| `first_name` | First name (required) |
| `last_name` | Last name (required) |
| `organization_name` | Company name (required if party_type: organization) |
| `organization_type` | Company type (required if party_type: organization) |
| `organization_user_id` | Link to org user (optional) |
| `contact_email_address` | Email for signing (optional) |
| `appears_as_party` | Show in contract document |
| `is_signature_required` | Require signature |
| `order` | Display/signing order |
| `referenced_as_within_document` | How party is referenced in text |

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

    // 2. Get prefix
    $prefixes = $client->contractPrefixes->list();
    $prefix = $prefixes->data[0] ?? $client->contractPrefixes->create([
        'name' => 'Service Agreements',
        'alias' => 'SA',
        'next_number' => 1,
        'padding' => 4,
    ]);

    // 3. Create the contract
    $contract = $client->contracts->create([
        'title' => 'Software Development Agreement',
        'organization_sender_user_id' => 'org_user_company_xxx',
        'organization_receiver_user_id' => 'org_user_client_xxx',
        'organization_contract_status_id' => $draftStatus->id,
        'organization_contract_prefix_id' => $prefix->id,
        'category' => 'core',
        'direction' => 'outbound',
        'source' => 'internal',
        'issued_at' => date('Y-m-d'),
        'ends_at' => date('Y-m-d', strtotime('+1 year')),

        'content_introduction' => 'This Agreement is entered into by and between the parties identified below.',

        'chapters' => [
            [
                'title' => 'Services',
                'content' => '<p>Provider will deliver custom software development services.</p>',
                'order' => 1,
            ],
            [
                'title' => 'Payment',
                'content' => '<p>Client agrees to pay EUR 50,000 for the services.</p>',
                'order' => 2,
            ],
            [
                'title' => 'Term',
                'content' => '<p>This agreement is effective for 12 months.</p>',
                'order' => 3,
            ],
        ],

        'parties' => [
            [
                'organization_user_id' => 'org_user_company_xxx',
                'party_type' => 'organization',
                'party_country_code' => 'RO',
                'first_name' => 'CEO',
                'last_name' => 'Name',
                'organization_name' => 'Our Company SRL',
                'organization_type' => 'SRL',
                'appears_as_party' => true,
                'is_signature_required' => true,
                'order' => 1,
                'referenced_as_within_document' => 'Provider',
            ],
            [
                'organization_user_id' => 'org_user_client_xxx',
                'party_type' => 'organization',
                'party_country_code' => 'US',
                'first_name' => 'Client',
                'last_name' => 'Representative',
                'organization_name' => 'Client Corp',
                'organization_type' => 'LLC',
                'contact_email_address' => 'client@example.com',
                'appears_as_party' => true,
                'is_signature_required' => true,
                'order' => 2,
                'referenced_as_within_document' => 'Client',
            ],
        ],
    ]);

    echo "Contract created: {$contract->id}\n";
    echo "Number: {$contract->number}\n";

    // 4. Request signatures
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

    // 5. Download PDF for review
    $pdf = $client->contracts->download($contract->id);
    file_put_contents("contract-{$contract->number}.pdf", $pdf);

    echo "Contract PDF saved\n";

    // 6. Later: Check signature status and activate
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

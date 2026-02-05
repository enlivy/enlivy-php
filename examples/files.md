# Files

Upload and manage files attached to various entities.

## Uploading Files

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Upload a file
$file = $client->files->create([
    'file' => fopen('/path/to/document.pdf', 'r'),
    'name' => 'Contract Document',
    'description' => 'Signed contract for Project X',
]);

echo "File uploaded: {$file->id}\n";
echo "URL: {$file->url}\n";
```

## Listing Files

```php
<?php

$files = $client->files->list([
    'per_page' => 50,
]);

foreach ($files as $file) {
    echo "{$file->name} ({$file->mime_type}) - {$file->size} bytes\n";
}
```

## Retrieving a File

```php
<?php

$file = $client->files->retrieve('org_file_xxx');

echo "Name: {$file->name}\n";
echo "Size: {$file->size}\n";
echo "Type: {$file->mime_type}\n";
echo "URL: {$file->url}\n";
```

## Deleting a File

```php
<?php

$client->files->delete('org_file_xxx');
```

## Attaching Files to Entities

Files can be attached to invoices, contracts, and other entities during creation or update.

```php
<?php

// Attach to invoice
$invoice = $client->invoices->create([
    'organization_receiver_user_id' => 'org_user_xxx',
    // ... other fields
    'attachments' => ['org_file_xxx', 'org_file_yyy'],
]);

// Attach to contract
$contract = $client->contracts->update('org_cont_xxx', [
    'attachments' => ['org_file_xxx'],
]);
```

## Related

- [Invoices](invoices.md) - Attach files to invoices
- [Contracts](contracts.md) - Attach files to contracts

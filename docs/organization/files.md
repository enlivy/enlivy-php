# Files

Upload and manage files attached to various entities. Enlivy uses a two-step upload process for secure file handling.

## Key Concepts

### Two-Step Upload Process

1. **Initialize upload**: Request an upload URL by specifying the file extension
2. **Complete upload**: After uploading to the presigned URL, complete the process with file metadata

This approach allows for direct-to-storage uploads without passing files through the API server.

## Uploading Files

### Step 1: Initialize Upload

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// Initialize upload by specifying the file extension
$upload = $client->files->create([
    'extension' => 'pdf',
]);

echo "Upload URL: {$upload->upload_url}\n";
echo "File ID: {$upload->id}\n";
```

### Step 2: Upload to Presigned URL

```php
<?php

// Upload the file directly to the presigned URL
$ch = curl_init($upload->upload_url);
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_INFILE, fopen('/path/to/document.pdf', 'r'));
curl_setopt($ch, CURLOPT_INFILESIZE, filesize('/path/to/document.pdf'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
```

### Step 3: Complete Upload

```php
<?php

// Complete the upload with file metadata
$file = $client->files->create([
    '_action' => 'completed',
    'source_file_name' => 'original_filename.pdf', // Original file name
    'name' => 'Contract Document',                  // Display name
    'description' => 'Signed contract for Project X',
    'parent_organization_files_id' => null,        // Optional: for folder structure
    'context' => 'contracts',                       // Optional: categorization
]);

echo "File completed: {$file->id}\n";
echo "Name: {$file->name}\n";
```

### Complete Upload Example

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$localFilePath = '/path/to/document.pdf';
$extension = pathinfo($localFilePath, PATHINFO_EXTENSION);
$originalName = basename($localFilePath);

// 1. Initialize
$upload = $client->files->create([
    'extension' => $extension,
]);

// 2. Upload to presigned URL
$ch = curl_init($upload->upload_url);
curl_setopt_array($ch, [
    CURLOPT_PUT => true,
    CURLOPT_INFILE => fopen($localFilePath, 'r'),
    CURLOPT_INFILESIZE => filesize($localFilePath),
    CURLOPT_RETURNTRANSFER => true,
]);
curl_exec($ch);
curl_close($ch);

// 3. Complete
$file = $client->files->create([
    '_action' => 'completed',
    'source_file_name' => $originalName,
    'name' => 'My Document',
    'description' => 'Description of the document',
]);

echo "Upload complete: {$file->id}\n";
```

## Listing Files

```php
<?php

$files = $client->files->list([
    'per_page' => 50,
]);

foreach ($files as $file) {
    echo "{$file->name}\n";
    echo "  Source: {$file->source_file_name}\n";
    echo "  Context: {$file->context}\n";
}
```

### Filter by Context

```php
<?php

$files = $client->files->list([
    'context' => 'contracts',
]);
```

### Include Parent Folder

```php
<?php

$files = $client->files->list([
    'include' => ['parent'],
]);

foreach ($files as $file) {
    $parent = $file->parent ? $file->parent->name : 'Root';
    echo "{$file->name} (in {$parent})\n";
}
```

## Retrieving a File

```php
<?php

$file = $client->files->retrieve('org_file_xxx');

echo "Name: {$file->name}\n";
echo "Original filename: {$file->source_file_name}\n";
echo "Description: {$file->description}\n";
echo "Context: {$file->context}\n";
echo "Created: {$file->created_at}\n";
```

## Updating a File

```php
<?php

$file = $client->files->update('org_file_xxx', [
    'name' => 'Updated Document Name',
    'description' => 'Updated description',
]);

echo "Updated: {$file->name}\n";
```

## Deleting a File

```php
<?php

// Soft delete
$file = $client->files->delete('org_file_xxx');

echo "Deleted at: {$file->deleted_at}\n";
```

## Restoring a File

```php
<?php

$file = $client->files->restore('org_file_xxx');

echo "Restored: {$file->name}\n";
```

## Creating Folder Structure

Files can be organized in a folder structure using `parent_organization_files_id`:

```php
<?php

// Create a folder (a file with no actual file content)
$folder = $client->files->create([
    '_action' => 'completed',
    'source_file_name' => 'contracts',
    'name' => 'Contracts',
    'description' => 'All contract documents',
]);

// Upload a file into the folder
$upload = $client->files->create(['extension' => 'pdf']);
// ... upload to presigned URL ...

$file = $client->files->create([
    '_action' => 'completed',
    'source_file_name' => 'contract.pdf',
    'name' => 'Service Agreement',
    'parent_organization_files_id' => $folder->id,
]);

echo "File in folder: {$file->name}\n";
```

## Supported File Extensions

Common supported extensions include:
- Documents: `pdf`, `doc`, `docx`, `xls`, `xlsx`, `ppt`, `pptx`
- Images: `jpg`, `jpeg`, `png`, `gif`, `webp`
- Archives: `zip`, `rar`
- Text: `txt`, `csv`

## Field Reference

### Initialize Upload Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `extension` | string | Yes | File extension (e.g., `pdf`, `jpg`) |

### Complete Upload Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `_action` | string | Yes | Must be `completed` |
| `source_file_name` | string | Yes | Original file name |
| `name` | string | Yes | Display name for the file |
| `description` | string | No | File description |
| `parent_organization_files_id` | string | No | Parent folder ID |
| `context` | string | No | Categorization context |

### Include Options

| Include | Description |
|---------|-------------|
| `parent` | Parent folder details |
| `organization` | Organization details |
| `deleted_by_user` | User who deleted |

## Complete Example: Document Upload System

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

function uploadFile($client, $filePath, $name, $description = null, $parentId = null) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $originalName = basename($filePath);

    // Initialize upload
    $upload = $client->files->create([
        'extension' => $extension,
    ]);

    // Upload to presigned URL
    $ch = curl_init($upload->upload_url);
    curl_setopt_array($ch, [
        CURLOPT_PUT => true,
        CURLOPT_INFILE => fopen($filePath, 'r'),
        CURLOPT_INFILESIZE => filesize($filePath),
        CURLOPT_RETURNTRANSFER => true,
    ]);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new \Exception("Upload failed with HTTP $httpCode");
    }

    // Complete upload
    $params = [
        '_action' => 'completed',
        'source_file_name' => $originalName,
        'name' => $name,
    ];

    if ($description) {
        $params['description'] = $description;
    }

    if ($parentId) {
        $params['parent_organization_files_id'] = $parentId;
    }

    return $client->files->create($params);
}

try {
    // Create a folder for contracts
    $contractsFolder = $client->files->create([
        '_action' => 'completed',
        'source_file_name' => 'contracts',
        'name' => 'Contracts 2026',
        'description' => 'All contracts for 2026',
        'context' => 'contracts',
    ]);

    echo "Created folder: {$contractsFolder->name}\n";

    // Upload documents to the folder
    $contract = uploadFile(
        $client,
        '/path/to/service-agreement.pdf',
        'Service Agreement - Acme Corp',
        'Annual service agreement with Acme Corporation',
        $contractsFolder->id
    );

    echo "Uploaded: {$contract->name}\n";

    // List all files in the folder
    $files = $client->files->list([
        'parent_organization_files_id' => $contractsFolder->id,
    ]);

    echo "\nFiles in {$contractsFolder->name}:\n";
    foreach ($files as $file) {
        echo "  - {$file->name}\n";
    }

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
```

## Related

- [Invoices](invoices.md) - Attach files to invoices
- [Contracts](contracts.md) - Attach files to contracts
- [Proposals](proposals.md) - Attach files to proposals

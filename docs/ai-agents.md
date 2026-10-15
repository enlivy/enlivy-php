# AI Agents

Run Enlivy's AI agents over your organization's records or over input you send. Agents are defined
by Enlivy: the SDK lists them, reads one, and runs one. It cannot create, change or delete an agent.

## Key Concepts

### Agent Architecture

```
AI Agent
    |
    +-- Definition (read-only)
    |       +-- Input Target Entity (optional record type it processes)
    |       +-- Input Data (custom input fields it accepts)
    |
    +-- Run (execute the agent)
    |       +-- Organization context
    |       +-- Entity ID (if it has a target entity)
    |       +-- Custom instructions
    |
    +-- Output (parsed result)
            +-- Text, JSON, or entity structure
```

### Output Formats

Each agent answers in one format:

| Format | Description |
|--------|-------------|
| `text` | Plain text response |
| `json` | Structured JSON response |
| `entity_json` | Single entity structure |
| `entities_json` | Multiple entities structure |

## Listing AI Agents

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$agents = $client->aiAgents->list();

foreach ($agents as $agent) {
    $title = $agent->title_lang_map['en'] ?? 'Untitled';
    $description = $agent->description_lang_map['en'] ?? '';

    echo "{$title} ({$agent->slug})\n";
    echo "  ID: {$agent->id}\n";
    echo "  Description: {$description}\n";
    echo "  Has Input Target: " . ($agent->has_input_target ? 'Yes' : 'No') . "\n";

    if ($agent->input_target_entity) {
        echo "  Target Entity: {$agent->input_target_entity}\n";
    }

    foreach ($agent->input_data ?? [] as $field) {
        echo "    - {$field['field_key']} ({$field['type']})\n";
    }
}
```

## Retrieving an AI Agent

```php
<?php

$agent = $client->aiAgents->retrieve('ai_agent_xxx');

echo "Agent: {$agent->id}\n";
echo "Title: " . ($agent->title_lang_map['en'] ?? '') . "\n";
echo "Description: " . ($agent->description_lang_map['en'] ?? '') . "\n";
echo "Has Input Target: " . ($agent->has_input_target ? 'Yes' : 'No') . "\n";
echo "Input Target Entity: {$agent->input_target_entity}\n";

foreach ($agent->input_data ?? [] as $field) {
    echo "  - {$field['field_key']}: {$field['title']}\n";
}
```

## Running an AI Agent

`run()` returns the agent's output itself, so a JSON answer's keys read straight off the result.

### Basic Run

```php
<?php

$result = $client->aiAgents->run('ai_agent_xxx', [
    'organization_id' => 'org_xxx',

    // Custom input fields (as listed in the agent's input_data)
    'input' => [
        'report_text' => 'Monthly Sales Report - January 2026 ...',
    ],

    // Optional additional instructions
    'instructions' => 'Report amounts in EUR',

    // Optional timezone for date processing
    'timezone' => 'Europe/Bucharest',
]);

echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
```

When a JSON agent's answer cannot be parsed, the result carries `raw` with the model's text instead.

### Run with Target Entity

```php
<?php

// For agents with an input_target_entity
$result = $client->aiAgents->run('ai_agent_xxx', [
    'organization_id' => 'org_xxx',

    // Required when the agent has an input_target_entity
    'organization_entity_id' => 'org_inv_xxx',

    'instructions' => 'Be conservative with confidence scores',
]);

print_r($result->toArray());
```

## Field Reference

### Run Request Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `organization_id` | string | Yes | Organization context |
| `organization_entity_id` | string | Conditional | Record to process (required if the agent has an `input_target_entity`) |
| `input` | object | No | Custom input values (matches the agent's `input_data`) |
| `instructions` | string | No | Additional instructions for this run |
| `timezone` | string | No | Timezone for date processing |

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Agent ID |
| `slug` | string | Stable agent name |
| `type` | string | Agent type |
| `title_lang_map` | object | Title by language |
| `description_lang_map` | object | Description by language |
| `has_input_target` | boolean | Whether the agent processes a record |
| `input_target_entity` | string | Target record type |
| `input_data` | array | Input field definitions |
| `entity_schemas` | array | (Admin only) Entity schemas |
| `created_at` | datetime | (Admin only) Creation timestamp |
| `updated_at` | datetime | (Admin only) Update timestamp |
| `deleted_at` | datetime | (Admin only) Deletion timestamp |

### Include Options

| Include | Description |
|---------|-------------|
| `deleted_by_user` | User who deleted the agent |

## Complete Example: Classify an Invoice

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Find an agent that processes invoices
    $agent = null;
    foreach ($client->aiAgents->list() as $candidate) {
        if ($candidate->input_target_entity === 'invoice') {
            $agent = $candidate;
            break;
        }
    }

    if ($agent === null) {
        exit("No invoice agent available\n");
    }

    // 2. Get an invoice to process
    $invoices = $client->invoices->list(['limit' => 1]);
    $invoice = $invoices->data[0];

    // 3. Run the agent on it
    $result = $client->aiAgents->run($agent->id, [
        'organization_id' => 'org_xxx',
        'organization_entity_id' => $invoice->id,
    ]);

    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Notes

- AI Agents require the `prompt_engine` feature to be enabled for your organization
- An agent's prompts are never returned by the API
- All agent runs are logged for billing and debugging purposes
- The `input_target_entity` determines which record type an agent processes

## Related

- [Invoices](organization/invoices.md) - Process invoices with AI
- [Contracts](organization/contracts.md) - Analyze contracts with AI
- [Prospects](organization/prospects.md) - AI-assisted prospect management

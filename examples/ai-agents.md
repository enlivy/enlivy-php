# AI Agents

Use AI-powered agents for entity processing, data transformation, and intelligent matching. AI Agents are configurable OpenAI-powered processors that transform input data into structured output.

## Key Concepts

### Agent Architecture

```
AI Agent System
    |
    +-- Agent Configuration (prompts, output format)
    |       +-- Input Target Entity (optional entity to process)
    |       +-- Input Data (custom input fields)
    |
    +-- Run (execute agent with input)
    |       +-- Organization context
    |       +-- Entity ID (if target entity)
    |       +-- Custom instructions
    |
    +-- Output (parsed result)
            +-- Text, JSON, or Entity structure
```

### Output Formats

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

    echo "{$title}\n";
    echo "  ID: {$agent->id}\n";
    echo "  Description: {$description}\n";
    echo "  Output Format: {$agent->output_format}\n";
    echo "  Has Input Target: " . ($agent->has_input_target ? 'Yes' : 'No') . "\n";

    if ($agent->input_target_entity) {
        echo "  Target Entity: {$agent->input_target_entity}\n";
    }

    if ($agent->input_data) {
        echo "  Input Fields:\n";
        foreach ($agent->input_data as $field) {
            echo "    - {$field['field_key']} ({$field['type']})\n";
        }
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
echo "Output Format: {$agent->output_format}\n";
echo "Has Input Target: " . ($agent->has_input_target ? 'Yes' : 'No') . "\n";
echo "Input Target Entity: {$agent->input_target_entity}\n";

if ($agent->input_data) {
    echo "Input Fields:\n";
    foreach ($agent->input_data as $field) {
        echo "  - {$field['field_key']}: {$field['title']}\n";
    }
}
```

## Creating an AI Agent

```php
<?php

$agent = $client->aiAgents->create([
    // Required
    'title_lang_map' => [
        'en' => 'Invoice Classifier',
    ],
    'description_lang_map' => [
        'en' => 'Classifies invoices into categories based on line items',
    ],

    // Optional - prompt configuration
    'system_prompt' => 'You are an invoice classifier. Analyze the invoice...',
    'output_format' => 'json',
    'output_structure' => [
        'category' => 'string',
        'confidence' => 'number',
        'tags' => 'array',
    ],

    // Optional - input configuration
    'input_target_entity' => 'invoice', // Process invoices
    'input_data' => [
        [
            'field_key' => 'additional_context',
            'type' => 'text',
            'title' => 'Additional Context',
        ],
    ],

    // Optional - entity schemas for context
    'entity_schemas' => ['invoice', 'line_item'],
]);

echo "Created agent: {$agent->id}\n";
```

## Running an AI Agent

### Basic Run

```php
<?php

$result = $client->aiAgents->run('ai_agent_xxx', [
    'organization_id' => 'org_xxx',

    // Custom input fields (if defined in agent's input_data)
    'input' => [
        'additional_context' => 'This is a monthly recurring invoice',
    ],

    // Optional additional instructions
    'instructions' => 'Focus on the primary service category',

    // Optional timezone for date processing
    'timezone' => 'Europe/Bucharest',
]);

echo "Result: " . json_encode($result->output) . "\n";
```

### Run with Target Entity

```php
<?php

// For agents with input_target_entity configured
$result = $client->aiAgents->run('ai_agent_xxx', [
    'organization_id' => 'org_xxx',

    // Required when agent has input_target_entity
    'organization_entity_id' => 'org_inv_xxx', // The invoice to process

    'input' => [
        'additional_context' => 'Quarterly billing cycle',
    ],
]);

echo "Classification: {$result->output['category']}\n";
echo "Confidence: {$result->output['confidence']}\n";
```

## Updating an AI Agent

```php
<?php

$agent = $client->aiAgents->update('ai_agent_xxx', [
    'title_lang_map' => [
        'en' => 'Updated Invoice Classifier',
    ],
    'system_prompt' => 'Updated prompt with improved instructions...',
    'output_format' => 'entity_json',
]);

echo "Updated agent: {$agent->id}\n";
```

## Deleting an AI Agent

```php
<?php

// Soft delete
$agent = $client->aiAgents->delete('ai_agent_xxx');

echo "Deleted at: {$agent->deleted_at}\n";
```

## Restoring an AI Agent

```php
<?php

$agent = $client->aiAgents->restore('ai_agent_xxx');

echo "Restored: {$agent->id}\n";
```

## Field Reference

### Required Fields

| Field | Type | Description |
|-------|------|-------------|
| `title_lang_map` | object | Agent title by language |
| `description_lang_map` | object | Agent description by language |

### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `system_prompt` | string | OpenAI system prompt instructions |
| `output_format` | string | Output type (text, json, entity_json, entities_json) |
| `output_structure` | object | Expected output JSON structure |
| `input_target_entity` | string | Entity type to process (invoice, contract, etc.) |
| `input_data` | array | Custom input field definitions |
| `entity_schemas` | array | Entity types to include in context |

### Run Request Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `organization_id` | string | Yes | Organization context |
| `organization_entity_id` | string | Conditional | Entity ID (required if agent has input_target_entity) |
| `input` | object | No | Custom input values (matches agent's input_data) |
| `instructions` | string | No | Additional instructions for this run |
| `timezone` | string | No | Timezone for date processing |

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Agent ID (ai_agent_xxx) |
| `title_lang_map` | object | Title by language |
| `description_lang_map` | object | Description by language |
| `output_format` | string | Output format type |
| `has_input_target` | boolean | Whether agent processes entities |
| `input_target_entity` | string | Target entity type |
| `input_data` | array | Input field definitions |
| `entity_schemas` | array | (Admin only) Entity schemas |
| `system_prompt` | string | (Admin only) System prompt |
| `output_structure` | object | (Admin only) Output structure |
| `created_at` | datetime | (Admin only) Creation timestamp |
| `updated_at` | datetime | (Admin only) Update timestamp |
| `deleted_at` | datetime | (Admin only) Deletion timestamp |

### Include Options

| Include | Description |
|---------|-------------|
| `deleted_by_user` | User who deleted the agent |

## Complete Example: Invoice Classification Workflow

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create a classification agent
    $agent = $client->aiAgents->create([
        'title_lang_map' => [
            'en' => 'Invoice Category Classifier',
        ],
        'description_lang_map' => [
            'en' => 'Automatically categorizes invoices based on line items and amounts',
        ],
        'system_prompt' => <<<PROMPT
You are an invoice classification assistant. Analyze the invoice provided and determine:
1. The primary category (Software, Hardware, Services, Subscription, Other)
2. Confidence level (0.0 to 1.0)
3. Relevant tags

Return your analysis as JSON.
PROMPT,
        'output_format' => 'json',
        'output_structure' => [
            'category' => 'string',
            'confidence' => 'number',
            'tags' => 'array',
            'reasoning' => 'string',
        ],
        'input_target_entity' => 'invoice',
        'entity_schemas' => ['invoice', 'invoice_line_item'],
    ]);

    echo "Agent created: {$agent->id}\n";

    // 2. Get an invoice to classify
    $invoices = $client->invoices->list(['per_page' => 1]);
    $invoice = $invoices->data[0];

    // 3. Run the classifier
    $result = $client->aiAgents->run($agent->id, [
        'organization_id' => 'org_xxx',
        'organization_entity_id' => $invoice->id,
        'instructions' => 'Be conservative with confidence scores',
    ]);

    // 4. Display results
    echo "\nClassification Results:\n";
    echo "  Invoice: {$invoice->id}\n";
    echo "  Category: {$result->output['category']}\n";
    echo "  Confidence: " . ($result->output['confidence'] * 100) . "%\n";
    echo "  Tags: " . implode(', ', $result->output['tags']) . "\n";
    echo "  Reasoning: {$result->output['reasoning']}\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Complete Example: Text to JSON Converter

```php
<?php

use Enlivy\Enlivy;
use Enlivy\EnlivyClient;
use Enlivy\Exception\ValidationException;

Enlivy::setApiKey('1|your_token');
Enlivy::setOrganizationId('org_xxx');

$client = new EnlivyClient();

try {
    // 1. Create a text-to-JSON agent (no target entity)
    $agent = $client->aiAgents->create([
        'title_lang_map' => [
            'en' => 'Report Parser',
        ],
        'description_lang_map' => [
            'en' => 'Converts unstructured report text into structured JSON',
        ],
        'system_prompt' => <<<PROMPT
Parse the provided report text and extract:
- Report date
- Total amount
- Key metrics
- Summary points

Return as structured JSON.
PROMPT,
        'output_format' => 'json',
        'output_structure' => [
            'report_date' => 'string',
            'total_amount' => 'number',
            'metrics' => 'object',
            'summary' => 'array',
        ],
        // No input_target_entity - uses custom input instead
        'input_data' => [
            [
                'field_key' => 'report_text',
                'type' => 'textarea',
                'title' => 'Report Text',
            ],
        ],
    ]);

    echo "Agent created: {$agent->id}\n";

    // 2. Run with custom input
    $result = $client->aiAgents->run($agent->id, [
        'organization_id' => 'org_xxx',
        'input' => [
            'report_text' => <<<REPORT
Monthly Sales Report - January 2026

Total Revenue: EUR 125,000
New Customers: 47
Returning Customers: 203

Key Highlights:
- 15% increase from December
- Enterprise segment grew 25%
- Average order value up to EUR 500
REPORT,
        ],
    ]);

    // 3. Use the parsed data
    echo "\nParsed Report:\n";
    echo json_encode($result->output, JSON_PRETTY_PRINT) . "\n";

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    print_r($e->getErrors());
}
```

## Notes

- AI Agents require the `openai` feature to be enabled for your organization
- Agent prompts and output structures are only visible to admin users
- All agent runs are logged for billing and debugging purposes
- The `input_target_entity` determines which entity types can be processed

## Related

- [Invoices](invoices.md) - Process invoices with AI
- [Contracts](contracts.md) - Analyze contracts with AI
- [Prospects](prospects.md) - AI-assisted prospect management

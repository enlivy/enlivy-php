# AI Agents

Use AI-powered features for entity processing and matching.

## Available AI Agents

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// List available agents
$agents = $client->aiAgents->list();

foreach ($agents as $agent) {
    echo "{$agent->name}: {$agent->description}\n";
    echo "  Type: {$agent->type}\n";
}
```

## Running an AI Agent

```php
<?php

$result = $client->aiAgents->run('ai_agent_xxx', [
    'input' => [
        'text' => 'Process this invoice data...',
    ],
]);

echo "Agent output: {$result->output}\n";
```

## Match Service

Find similar or related entities using AI matching.

### Match Prospects

```php
<?php

$matches = $client->match->prospects([
    'query' => 'Looking for web development services',
    'limit' => 10,
]);

foreach ($matches as $match) {
    echo "{$match->prospect->title} - Score: {$match->score}\n";
}
```

### Match Products

```php
<?php

$matches = $client->match->products([
    'query' => 'consulting services',
    'limit' => 5,
]);

foreach ($matches as $match) {
    echo "{$match->product->name} - Score: {$match->score}\n";
}
```

## Analytics

Get AI-powered insights and analytics.

```php
<?php

$analytics = $client->analytics->overview();

echo "Total Revenue: {$analytics->total_revenue}\n";
echo "Active Prospects: {$analytics->active_prospects}\n";
echo "Conversion Rate: {$analytics->conversion_rate}%\n";
```

## Search

Full-text search across entities.

```php
<?php

$results = $client->search->query([
    'q' => 'website development project',
    'types' => ['prospects', 'contracts', 'invoices'],
    'limit' => 20,
]);

foreach ($results as $result) {
    echo "[{$result->type}] {$result->title}\n";
}
```

## Related

- [Prospects](prospects.md) - AI-assisted prospect management

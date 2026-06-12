# Event Trails

An **event trail** is the read-only audit history of a record — who changed it,
when, from where, and exactly which fields moved. Trails are available for
**invoices**, **receipts** and **billing schedules**.

Each trail entry has an `event_type` (what happened), an `origin` (where it came
from), an optional acting organization user, and — when you request the `changes`
include — the field-level before/after values.

## Listing a record type's trail

Event trails are exposed on the parent service via `eventTrails()` and
`retrieveEventTrail()`:

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

// All invoice trail entries
$trail = $client->invoices->eventTrails([
    'include' => 'changes,actor_organization_user',
]);

foreach ($trail as $entry) {
    echo "{$entry->occurred_at}  {$entry->event_type}  (via {$entry->origin})\n";
}
```

The same methods exist on `$client->receipts` and `$client->billingSchedules`.

## Filtering

```php
<?php

$trail = $client->billingSchedules->eventTrails([
    'subject_id'  => 'org_bsch_xxx',           // a specific record
    'event_type'  => ['charged', 'charge_failed'],
    'origin'      => ['cron'],
    'occurred_from' => '2026-06-01',
    'occurred_to'   => '2026-06-30',
]);
```

| Filter | Description |
|--------|-------------|
| `subject_id` | Limit to a single record |
| `event_type` | One or more `EventTrail\EventType` values |
| `origin` | One or more `EventTrail\Origin` values |
| `occurred_from` / `occurred_to` | Time window on `occurred_at` |

## Inspecting field-level changes

```php
<?php

$entry = $client->invoices->retrieveEventTrail('org_evt_trl_xxx', [
    'include' => 'changes',
]);

foreach ($entry->changes as $change) {
    foreach ($change['changed_fields'] as $field) {
        $old = $change['old_values'][$field] ?? null;
        $new = $change['new_values'][$field] ?? null;
        echo "{$field}: {$old} → {$new}\n";
    }
}
```

Available includes: `changes`, `actor_organization_user`, `charge_log`.

## Event types & origins

`event_type` is one of `Enlivy\Enums\EventTrail\EventType`:

`created`, `updated`, `status_changed`, `deleted`, `restored`, `finalized`,
`charged`, `charge_failed`, `payment_method_changed`, `cancelled`,
`payment_generated`, `prorated_adjustment_generated`, `line_item_changed`,
`tax_breakdown_changed`, `phase_changed`, `scheduled_payment_changed`.

`origin` is one of `Enlivy\Enums\EventTrail\Origin`: `back_office`,
`client_portal`, `cron`, `webhook`, `system`.

## Field reference

| Field | Type | Description |
|-------|------|-------------|
| `subject_type` | string | The record type, e.g. `invoice`, `receipt`, `billing_schedule` |
| `subject_id` | string | The record the entry belongs to |
| `event_type` | string | What happened (`EventTrail\EventType`) |
| `origin` | string | Where it originated (`EventTrail\Origin`) |
| `actor_organization_user_id` | string\|null | The acting organization user, if any |
| `organization_invoice_charge_log_id` | string\|null | Linked charge log, when relevant |
| `metadata` | array\|null | Additional context |
| `occurred_at` | string | When the event occurred |

## Related

- [Event Destinations](event-destinations.md) — deliver these events to a webhook or Slack
- [Invoices](invoices.md), [Receipts](receipts.md)

# Trash

Deleting a record in Enlivy is a soft delete: the row leaves your lists but is still held, and a retention sweep removes it for good once its window expires. This endpoint shows what is being held and lets you empty it early.

## What is in the trash

```php
$inventory = $client->trashedItems->list();

echo "{$inventory->total_items} items, {$inventory->reclaimable_bytes} bytes reclaimable\n";

foreach ($inventory->entities as $entity) {
    echo "{$entity['entity']}: {$entity['count']} items";
    echo $entity['purgeable'] ? " (can be emptied)\n" : " (held)\n";
}
```

The report is a raw payload, not a record collection. Entities with nothing in the trash are omitted entirely.

| Field | Notes |
|-------|-------|
| `total_items` | Rows across every entity |
| `total_collateral_items` | Live rows that would go with them — a trashed project still has active tasks under it |
| `reclaimable_bytes` | Disk that emptying would actually free; counts only purgeable entities |
| `entities[].entity` | The entity key, and what you pass to `purge()` |
| `entities[].count` | Trashed rows |
| `entities[].bytes` | Storage held, or `null` for entities that hold no files |
| `entities[].retention_hours` | The configured window for that entity |
| `entities[].oldest_deleted_at` | When the oldest row was deleted |
| `entities[].purges_at` | When the sweep becomes entitled to take it |
| `entities[].purgeable` | Whether `purge()` can reach it |

`purges_at` is when the window expires, not a promise. A row still referenced by something live — a product on an issued invoice line — survives every sweep until that reference goes.

## Emptying the trash

```php
// Everything self-service can reach
$result = $client->trashedItems->purge();

// Or just the entities you name
$result = $client->trashedItems->purge([
    'entities' => ['files', 'products', 'tags'],
]);

echo "deleted {$result->deleted}, blocked {$result->blocked}, errored {$result->errored}\n";
```

**This cannot be undone.** It ignores the retention window by design: the window protects you from your own accidental delete, and asking to empty the trash is you saying you no longer need it.

| Field | Notes |
|-------|-------|
| `deleted` | Rows permanently removed |
| `blocked` | Rows a live reference still holds |
| `errored` | Rows whose deletion raised |
| `entities` | Per-entity counts, keyed by entity |

## What you can and cannot empty

Records held for statutory reasons are never reachable here, whatever you pass in `entities`:

> proposals · invoices · payslips · receipts · billing schedules · network exchanges · contracts · contract signatures · bank transactions · bank accounts · users · organizations

They still appear in `list()` with `purgeable => false`, so you can see what is being held and why the byte count is lower than the item count suggests. They are removed by the retention sweep on their own schedule.

Everything else is self-service:

> bank transaction cost types · invoice prefixes · receipt prefixes · contract prefixes · billing packages · products · tax rates · tax classes · payslip schemas · report schemas · reports · playbooks · guidelines · prospects · prospect activities · prospect statuses · prospect status paths · projects · tags · tasks · event destinations · files · user addresses · user bank accounts · user roles

Passing an entity outside that list is a 422, not a silent skip.

Both calls need the `organization.manage` ability.

## Related

- [Files](files.md) — usually the bulk of what is reclaimable
- [Organization Users](users.md) — held, not self-service

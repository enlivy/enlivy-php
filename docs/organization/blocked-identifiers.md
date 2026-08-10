# Blocked Identifiers

Keep an email address, an email domain or a phone number out of your organization. A blocked identifier stops registration, portal-session creation and other inbound flows before a record is created.

Two lists apply at once: the platform-wide list Enlivy maintains, and your organization's own. Both are enforced; you can only edit yours.

## Listing

```php
use Enlivy\Enums\BlockedIdentifier\Sources;
use Enlivy\Enums\BlockedIdentifier\Types;

// Your organization's own entries
$mine = $client->blockedIdentifiers->list();

// Yours plus the platform defaults
$all = $client->blockedIdentifiers->list([
    'source' => Sources::ALL->value,
    'type'   => [Types::EMAIL->value, Types::EMAIL_DOMAIN->value],
]);

foreach ($all->getData() as $row) {
    echo "{$row->type}  {$row->value}  ({$row->source})\n";
}
```

| Filter | Notes |
|--------|-------|
| `source` | `organization` (default), `platform`, or `all` |
| `type` | Array of `email`, `email_domain`, `phone_number` |
| `q` | Searches `value` and `normalized_value` — see the matching note below |

`Sources::ALL` is a query directive, not a value a row carries: a stored row is always `organization` or `platform`.

## Adding an entry

```php
$client->blockedIdentifiers->create([
    'type'   => Types::EMAIL_DOMAIN->value,
    'value'  => 'spam.example',
    'reason' => 'Repeated chargebacks',
]);
```

| Field | Notes |
|-------|-------|
| `type` | Required |
| `value` | Required; validated against the shape `type` implies, and unique within your organization |
| `reason` | Optional free text, max 255 |

`normalized_value` is derived server-side and is the key matching actually runs on — a number stored as `+40 746 047 047` is still found by its digits alone. It is read-only, as is `created_by_user_id`.

On update, sending `type` requires sending `value` with it: the shape rule applies to the pair, not to whichever half arrives.

```php
$client->blockedIdentifiers->update($id, ['reason' => 'Resolved — monitoring']);
$client->blockedIdentifiers->delete($id);
```

Platform entries are read-only. They appear under `source => 'all'` so you can see what is already covered before adding your own.

## Checking a value

Ask whether something is blocked without submitting a form — useful for validating a field as the user types:

```php
$answer = $client->misc->determineIsEmailBlocked(['value' => 'someone@spam.example']);

if ($answer->is_blocked) {
    echo "Blocked by the {$answer->source} list ({$answer->type})\n";
}
```

```php
$answer = $client->misc->determineIsPhoneNumberBlocked([
    'value'        => '0746047047',
    'country_code' => 'RO',   // needed when the number is not in international form
]);
```

Both answer the same shape:

| Field | Notes |
|-------|-------|
| `is_blocked` | The only field always populated |
| `type` | Which rule matched — an email can be caught as itself or by its domain |
| `source` | `organization` or `platform` |
| `value` | The stored entry that matched, not the value you sent |
| `reason` | Whatever was recorded on that entry |

Everything but `is_blocked` is `null` when nothing matched.

## Where blocking is enforced

Adding an entry takes effect immediately across the inbound surface — new-user registration and customer-portal session creation both reject a blocked email, and registration rejects a blocked phone number. Expect a 422 on those endpoints rather than a silent drop.

Blocking does not remove records that already exist. Review your existing users separately.

## Related

- [Organization Users](users.md) — the records these rules gate
- [Customer Portal](customer-portal.md) — portal session creation
- [Enums](../enums.md) — `BlockedIdentifier\Types`, `BlockedIdentifier\Sources`

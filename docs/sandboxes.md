# Sandboxes

A sandbox is a second organization that mirrors a live one's configuration but never reaches the outside world. Bill against it, email from it, sign contracts in it — nothing leaves the platform.

Every organization reports which side of that line it sits on:

```php
use Enlivy\Enums\Organization\Environments;

$organization = $client->organizations->retrieve('org_xxx');

if ($organization->environment === Environments::SANDBOX->value) {
    // safe to experiment
}
```

| Value | Meaning |
|-------|---------|
| `live` | Real organization. Outbound calls go out. |
| `sandbox` | Mirror of a live organization. Outbound calls are blocked. |

## Creating a Sandbox

```php
$sandbox = $client->organizations->createSandbox('org_xxx', [
    'name' => 'Acme — Staging',
]);

echo $sandbox->id;                  // its own organization id
echo $sandbox->organization_id;     // the live organization it mirrors
echo $sandbox->environment;         // 'sandbox'
```

The sandbox is a full organization with its own id, so point a client at it the same way you would any other:

```php
$sandboxClient = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => $sandbox->id,
]);
```

Two rules are enforced server-side, both answered as a 422:

- A sandbox cannot itself be copied — mirrors do not nest.
- An organization may hold only a small number of sandboxes at a time.

## What Carries Over

Enough for the sandbox to behave like the organization it mirrors, and nothing that would let it act on that organization's behalf.

**Copied:** legal identity and address, company information, locale and currency settings, branding colours, accounting contact details — and the whole tax configuration: registrations, filing jurisdictions and types, tax classes with their rates, and the tax-behaviour settings.

Tax configuration is copied because it is what decides whether a sale is taxed at all, and at what rate. A sandbox that started empty would charge differently from the organization it mirrors, which would defeat the point of rehearsing there.

**Not copied:** records — invoices, receipts, contracts, prospects, users, products. Also not copied: filing periods and tax events, which are the ledger the configuration produces rather than part of it; the sandbox builds its own from its own documents.

**Not inherited:** connected third-party credentials, and the portal domain. A sandbox starts with no integrations of its own and its `feature_list` reflects that — features that depend on a credential are withheld until the sandbox connects its own. Connect a test-mode Stripe account to the sandbox and the feature comes back.

## The Outbound Block

Anything that would reach a third party from a sandbox fails loudly rather than pretending to succeed — a silent no-op would let a sandbox report a charge that never happened. Expect an error, not a success, when a sandbox tries to:

- charge a card or move money
- send mail to an address outside the allowlist
- call a connected accounting, banking, or e-invoicing service

Everything internal — issuing documents, computing tax, running billing schedules, event delivery inside the platform — works exactly as it does live.

Write your integration tests to expect the failure. A test that passes in a sandbox only because the call silently did nothing would not have told you anything.

## Related

- [Authentication](authentication.md) — pointing a client at an organization
- [Taxes](organization/taxes.md) — the configuration a sandbox inherits
- [Integrations](integrations.md) — connecting a sandbox's own credentials
- [Enums](enums.md) — `Organization\Environments`

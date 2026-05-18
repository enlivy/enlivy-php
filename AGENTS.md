# Contributing to enlivy-php (agents & humans)

`enlivy-php` is the official PHP SDK for the Enlivy API. It is a **public,
open-source library** (MIT, PHP 8.3+). API reference: https://docs.enlivy.com/api.

This file is the canonical guidance for anyone — human or AI agent — making
changes here.

---

## Two non-negotiable principles

1. **This is a public repository; the upstream API is private.** Never let the
   API's internal implementation be inferable from this SDK. Do not put internal
   namespaces, internal class/route/file names, internal helper-method names,
   infrastructure (IPs, hosts, DB), or non-public hostnames into any committed
   file — code, comments, docblocks, docs, tests, or generated output. Only the
   public HTTP surface, this SDK's own classes (`Enlivy\…`), public config
   (`ENLIVY_API_KEY`, `https://api.enlivy.com`) and public concepts may appear.
   Generic consumer-side framework scaffolding in integration examples is fine.
   An automated guard enforces this before merge — run it (see *Quality gate*).

2. **Comments are minimal.** A comment must explain a non-obvious *why*. Never
   narrate what the code is, restate a class name, or describe where a value
   came from upstream. Mirrored enums and resource classes carry **no docblock**
   unless there is a real caveat — the name and namespace are self-documenting.

---

## Architecture

```
src/
├── Enlivy.php                  # Global config singleton
├── EnlivyClient.php            # Main entry point
├── EnlivyPortalClient.php      # Client Portal entry point
├── {GlobalResource}.php        # Global resources (User, Organization, …)
├── Organization/               # Organization-scoped resources
├── Enums/                      # String-backed enums (mirror public value sets)
└── Service/
    ├── {GlobalService}.php
    ├── OAuth/
    ├── Organization/           # Organization-scoped services
    └── ClientPortal/           # Customer-portal services
```

### Namespace separation

- **Global** resources/services live at root: `Enlivy\User`,
  `Enlivy\Service\OrganizationService`.
- **Organization-scoped** live under `Enlivy\Organization\…` and
  `Enlivy\Service\Organization\…`.
- **Client Portal** services live under `Enlivy\Service\ClientPortal\…`.

### Configuration

```php
// Global
\Enlivy\Enlivy::setApiKey('1|token');
\Enlivy\Enlivy::setOrganizationId('org_xxx');
$client = new \Enlivy\EnlivyClient();

// Per-client
$client = new \Enlivy\EnlivyClient([
    'api_key' => '1|token',
    'organization_id' => 'org_xxx',
]);

// Client Portal
$portal = new \Enlivy\EnlivyPortalClient([
    'portal_token' => 'session-token',
    'organization_id' => 'org_xxx',
]);
```

### Public concepts

- **OrganizationUser vs Prospect** — a Prospect is a CRM lead and cannot be
  invoiced; create an OrganizationUser (with an invoiceable role) to bill.
- **Invoice `source`** — `internal` (generated) or `external` (uploaded);
  **`direction`** — `outbound` (you → customer) or `inbound`.
- **IDs** use `{prefix}_{ksuid}` (e.g. `org_`, `org_user_`, `org_inv_`).

---

## Conventions

### Includes & filters

- Include keys are always `snake_case` in `AVAILABLE_INCLUDES` and must match
  the API exactly.
- Global filters (`q`, `q_in`, `ids`, `order_by`, `order`, `page`, `per_page`,
  `deleted`, `tag_ids`) are handled by the `HasFilters` trait — never redeclare
  them in a service's `AVAILABLE_FILTERS`.
- Date-range filters (`created_at_from/to`, `updated_at_from/to`, …) are **not**
  global; only declare them on services whose endpoint actually supports them.
- Only declare includes/filters the API actually accepts for that entity. Don't
  copy them between services.

### Enums

String-backed enums under `Enlivy\Enums\…` mirror the fixed value sets the API
accepts/returns. Use them for type-safe requests and validation. The
`Enlivy\Enums\Concern\EnumValues` trait adds `values()`, `names()`, `isValid()`
on top of native `cases()`/`from()`/`tryFrom()`. Locale-dependent display
labels are **not** mirrored — those are a runtime concern. See `docs/enums.md`.

### Service template

```php
class EntityService extends AbstractService
{
    use HasIncludes;   // every service
    use HasFilters;    // every service with list()

    protected const string RESOURCE = 'entities';
    protected const ?string RESOURCE_CLASS = Entity::class;

    public const array AVAILABLE_INCLUDES = ['organization'];
    public const array AVAILABLE_FILTERS = ['status'];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Entity
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
    // create / update / delete follow the same shape
}
```

Mandatory: every service uses `HasIncludes`; every service with `list()` also
uses `HasFilters`; every non-list method accepting `$params` with possible
includes calls `$this->validateIncludes($params)`. Optional concern traits:
`HasRestore`, `HasTagging`, `HasDownload`, `HasImports`, `HasReorder`.

### Adding a resource/service

1. Create the resource class (`src/…` or `src/Organization/…`), set
   `OBJECT_NAME`.
2. Register it in `src/Util/ObjectTypes.php`.
3. Create the service from the template above.
4. Register it in the relevant service factory.
5. Add the `@property` line to the client docblock.
6. Add/extend the matching page in `docs/`.
7. Run the quality gate.

When modifying an existing feature, update the service (includes/filters/
methods), the resource if fields changed, the matching `docs/*.md`, and remove
any stale PHPDoc.

---

## Quality gate (run before every change is done)

```bash
composer test          # PHPUnit — must be green
composer stan          # PHPStan level 3 — zero new errors
composer qa            # both of the above
```

Maintainers additionally run an internal hygiene check before merge that fails
on any reference to the upstream API's internals (principle 1). You don't need
that tool to contribute — just follow the two principles above: never commit
anything that references the upstream's internal implementation, and keep
comments minimal.

## Common mistakes

| Mistake | Consequence |
|---|---|
| camelCase include keys | SDK allows includes the API rejects |
| Adding a date-range filter where unsupported | SDK validates a filter the API ignores |
| Missing `HasIncludes`/`HasFilters` | invalid includes/filters reach the API silently |
| Copying includes/filters between services | wrong contract for the entity |
| Provenance/verbose comments | leaks internal info; violates principle 2 |
| Stale PHPDoc after removing an include/filter | misleading public docs |

## Documentation

Each `docs/*.md` covers a feature: overview, prerequisites, runnable examples,
gotchas, related resources — in public terms only.

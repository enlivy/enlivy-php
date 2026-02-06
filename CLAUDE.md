# Enlivy PHP SDK - Claude Agent Instructions

> **Self-Maintaining Documentation**: This file and the `examples/` directory must be kept in sync with the Enlivy API. When new features are added to the API, corresponding SDK methods and documentation must be added here.

---

## Mandatory Rules

**These rules are NON-NEGOTIABLE. Every change to the SDK MUST follow them.**

1. **All include keys are `snake_case`.** Never use camelCase in `AVAILABLE_INCLUDES`. The API expects snake_case include keys; the SDK must match exactly.
2. **Every service with a `list()` method MUST use both `HasIncludes` and `HasFilters` traits.** No exceptions.
3. **Every non-list method that accepts `$params` with potential includes MUST call `$this->validateIncludes($params)`.** This includes `retrieve()`, `create()`, `update()`, and `delete()`.
4. **Run tests and static analysis after every change.** `./vendor/bin/phpunit` and `./vendor/bin/phpstan analyse -l 3 src/` must both pass with zero new errors.
5. **No stale PHPDoc comments.** If you remove a filter or include from a constant, also remove it from any docblock that references it.

---

## Project Overview

**enlivy-php** is the official PHP SDK for the Enlivy API.

- **Company**: Enlivy SRL (Romania, VAT: RO44824071)
- **License**: MIT
- **PHP Version**: 8.3+
- **Repository**: https://github.com/enlivy/enlivy-php

---

## Architecture Principles

### 1. SDK Structure

```
src/
├── Enlivy.php                    # Global config singleton
├── EnlivyClient.php              # Main entry point
├── {GlobalResource}.php          # Global resources (User, Organization, AiAgent, etc.)
├── Organization/                 # Organization-scoped resources
│   ├── Invoice.php
│   ├── Prospect.php
│   └── User.php                  # Organization User
└── Service/
    ├── {GlobalService}.php       # Global services
    ├── OAuth/                    # OAuth services
    └── Organization/             # Organization-scoped services
        ├── Invoice/
        ├── Prospect/
        └── ...
```

### 2. Namespace Separation

**Global resources** (not organization-scoped) live at root `Enlivy\`:
```php
namespace Enlivy;

class User extends ApiResource { ... }         // Global user account
class Organization extends ApiResource { ... }
class AiAgent extends ApiResource { ... }
```

**Organization-scoped resources** live in `Enlivy\Organization\`:
```php
namespace Enlivy\Organization;

class Invoice extends ApiResource { ... }
class Prospect extends ApiResource { ... }
class User extends ApiResource { ... }         // Organization user (customer/employee)
```

### 3. Services Follow Same Pattern

**Global services** at `Service/`:
```
Service/UserService.php              # Enlivy\Service\UserService
Service/OrganizationService.php      # Enlivy\Service\OrganizationService
Service/OAuth/OAuthClientService.php # Enlivy\Service\OAuth\OAuthClientService
```

**Organization-scoped services** at `Service/Organization/`:
```
Service/Organization/Invoice/InvoiceService.php
Service/Organization/Prospect/ProspectService.php
Service/Organization/UserService.php  # For organization users
```

### 4. Global Config Pattern

```php
// Users can configure globally
\Enlivy\Enlivy::setApiKey('1|token');
\Enlivy\Enlivy::setOrganizationId('org_xxx');
$client = new \Enlivy\EnlivyClient();

// Or per-client
$client = new \Enlivy\EnlivyClient([
    'api_key' => '1|token',
    'organization_id' => 'org_xxx',
]);
```

---

## Key Concepts

### OrganizationUser vs Prospect

| Entity | Purpose | Can Receive Invoices? |
|--------|---------|----------------------|
| **OrganizationUser** | Actual customer/client in the system | Yes (if role allows) |
| **Prospect** | Sales lead in pipeline (CRM) | No - must be linked/converted first |

To invoice someone, create an **OrganizationUser** with a role that has `can_be_invoiced = true`.

### Invoice Source & Direction

| Field | Values | Meaning |
|-------|--------|---------|
| `source` | `internal` | Generated in Enlivy |
| `source` | `external` | Uploaded from elsewhere |
| `direction` | `outbound` | You send to customer |
| `direction` | `inbound` | Customer sends to you |

### KSUID Prefixes

All IDs use format `{prefix}_{ksuid}`:
- `org_` - Organization
- `org_user_` - OrganizationUser
- `org_inv_` - Invoice
- `org_cont_` - Contract
- `org_pros_` - Prospect

---

## File Structure

```
enlivy-php/
├── src/
│   ├── Enlivy.php                        # Global config singleton
│   ├── EnlivyClient.php                  # Main client
│   ├── BaseEnlivyClient.php              # Client implementation
│   ├── ApiRequestor.php                  # HTTP requests
│   ├── Collection.php                    # Paginated results
│   ├── User.php, Organization.php, ...   # Global resources (~10)
│   ├── Organization/                     # Organization-scoped resources (~46)
│   │   ├── Invoice.php
│   │   ├── Prospect.php
│   │   ├── User.php                      # Organization User
│   │   └── ...
│   ├── Auth/                             # Authentication handlers
│   ├── Exception/                        # Exception classes
│   ├── HttpClient/                       # HTTP client interface
│   ├── Service/                          # API services
│   │   ├── UserService.php, ...          # Global services
│   │   ├── OAuth/                        # OAuth services
│   │   ├── Organization/                 # Organization-scoped services
│   │   │   ├── Invoice/InvoiceService.php
│   │   │   ├── Prospect/ProspectService.php
│   │   │   └── ...
│   │   └── Concern/                      # Shared traits
│   ├── Util/                             # Utilities
│   └── Webhook/                          # Webhook handling
├── tests/
├── examples/                             # Usage documentation
├── CLAUDE.md                             # This file
├── README.md                             # User-facing readme
├── LICENSE                               # MIT License
└── composer.json
```

---

## Includes & Filters Standards

### Include Keys — Always `snake_case`

The API expects all include keys in `snake_case`. The SDK's `AVAILABLE_INCLUDES` constants must match exactly.

```php
// ❌ WRONG — camelCase
public const array AVAILABLE_INCLUDES = [
    'deletedByUser',
    'organizationProject',
];

// ✅ CORRECT — snake_case
public const array AVAILABLE_INCLUDES = [
    'deleted_by_user',
    'organization_project',
];
```

### Global Filters (Automatic)

The `HasFilters` trait provides these **GLOBAL_FILTERS** that all list endpoints accept automatically:

```php
public const array GLOBAL_FILTERS = [
    'q', 'q_in', 'ids', 'order_by', 'order', 'page', 'per_page', 'deleted', 'tag_ids',
];
```

These do NOT need to be declared in any service's `AVAILABLE_FILTERS`.

Additionally, these keys are silently bypassed (not treated as filters):
- `include` (handled by HasIncludes)
- `organization_id` (handled by resolveOrganizationId)

### Resource-Specific Filters

Only filters the API actually supports for a given entity should be declared in `AVAILABLE_FILTERS`. Do not copy filters from one service to another — each entity has its own set.

### Date Range Filters — NOT Global

`created_at_from`, `created_at_to`, `updated_at_from`, `updated_at_to` are **NOT** global. They are only available on specific entities. As of 2026-02-06, only these entities support them:

- Organization
- Invoice
- BankTransaction
- Payslip
- Prospect
- Receipt
- ReusableContent

**Never assume** a new entity supports date range filters.

---

## Self-Maintenance Rules

### When Adding New API Endpoints

#### For Organization-Scoped Resources (most common)

1. **Create Resource Class**:
   ```php
   // src/Organization/NewEntity.php
   namespace Enlivy\Organization;

   class NewEntity extends \Enlivy\ApiResource
   {
       public const ?string OBJECT_NAME = 'new_entity';
   }
   ```

2. **Add to ObjectTypes**:
   ```php
   // src/Util/ObjectTypes.php
   use Enlivy\Organization\NewEntity;
   // ...
   'new_entity' => NewEntity::class,
   ```

3. **Create Service Class** (see full template in Service Patterns below):
   ```php
   // src/Service/Organization/NewEntity/NewEntityService.php
   namespace Enlivy\Service\Organization\NewEntity;

   use Enlivy\Service\AbstractService;
   use Enlivy\Service\Concern\HasIncludes;    // MANDATORY
   use Enlivy\Service\Concern\HasFilters;     // MANDATORY if service has list()

   class NewEntityService extends AbstractService
   {
       use HasIncludes;
       use HasFilters;

       public const array AVAILABLE_INCLUDES = [
           'organization',
       ];

       public const array AVAILABLE_FILTERS = [
           'status',
       ];
       // ...
   }
   ```

4. **Register in CoreServiceFactory**:
   ```php
   use Enlivy\Service\Organization\NewEntity\NewEntityService;
   // ...
   'newEntities' => NewEntityService::class,
   ```

5. **Update EnlivyClient PHPDoc**:
   ```php
   @property Service\Organization\NewEntity\NewEntityService $newEntities
   ```

6. **Add/Update Documentation** in `examples/`:
   - Create new file if new domain
   - Update existing file if extending feature

#### For Global Resources (rare)

1. **Create Resource Class** at `src/NewEntity.php` with namespace `Enlivy`
2. **Create Service Class** at `src/Service/NewEntityService.php` with namespace `Enlivy\Service`
3. Follow same registration steps

### When Modifying Existing Features

1. Update relevant Service class (includes, filters, methods)
2. Update Resource class if fields changed
3. Update corresponding `examples/*.md` file
4. Remove any stale PHPDoc comments that reference removed includes/filters
5. Run tests: `./vendor/bin/phpunit`
6. Run PHPStan: `./vendor/bin/phpstan analyse -l 3 src/`

### Verification Checklist (Run After Every Change)

```bash
# 1. PHP syntax check on changed files
php -l src/Path/To/ChangedFile.php

# 2. Run full test suite — must be 0 failures
./vendor/bin/phpunit

# 3. Static analysis — must be 0 new errors
./vendor/bin/phpstan analyse -l 3 src/

# 4. Dump autoload if new classes were added
composer dump-autoload
```

### Documentation Standards

Each `examples/*.md` file should include:
- Overview of the feature
- Prerequisites (what must exist first)
- Code examples with comments
- Common patterns and gotchas
- Related resources/services

---

## Service Patterns

### Standard CRUD Service (Complete Template)

**Every service MUST follow this pattern exactly.** Copy this template when creating new services.

```php
class EntityService extends AbstractService
{
    use HasIncludes;   // MANDATORY on every service
    use HasFilters;    // MANDATORY on every service with a list() method

    protected const string RESOURCE = 'entities';
    protected const ?string RESOURCE_CLASS = Entity::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'related_entity',
        'deleted_by_user',
    ];

    /**
     * Do NOT add global filters here (q, q_in, ids, order_by, order,
     * page, per_page, deleted, tag_ids) — they are handled automatically.
     *
     * Do NOT add date range filters (created_at_from/to, updated_at_from/to)
     * unless the entity actually supports them.
     */
    public const array AVAILABLE_FILTERS = [
        'status',
    ];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);   // MANDATORY
        $this->validateFilters($params);    // MANDATORY on list()
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Entity
    {
        $this->validateIncludes($params);   // MANDATORY
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Entity
    {
        $this->validateIncludes($params);   // MANDATORY
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Entity
    {
        $this->validateIncludes($params);   // MANDATORY
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Entity
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}
```

### Available Concerns (Traits)

| Trait | Methods | Use When | Mandatory? |
|-------|---------|----------|------------|
| `HasIncludes` | `validateIncludes()` | Always | **YES — every service** |
| `HasFilters` | `validateFilters()` | Service has `list()` | **YES — every service with list()** |
| `HasRestore` | `restore()` | Entity supports soft-delete recovery | No |
| `HasTagging` | `tag()`, `untag()` | Entity supports tagging | No |
| `HasDownload` | `download()` | Entity has PDF/file download | No |
| `HasImports` | `import()`, `importProgress()` | Entity supports bulk import | No |
| `HasReorder` | `reorder()` | Entity supports manual ordering | No |

---

## Common Mistakes to Avoid

| Mistake | Consequence |
|---------|------------|
| Using camelCase in `AVAILABLE_INCLUDES` | SDK allows includes the API rejects |
| Adding `created_at_from/to` to a service that doesn't support it | SDK validates a filter the API ignores |
| Forgetting `HasIncludes` on a service | No validation — invalid includes reach the API silently |
| Forgetting `HasFilters` on a `list()` method | No validation — invalid filters reach the API silently |
| Copy-pasting includes/filters from another service | Different entities have different includes/filters |
| Leaving stale PHPDoc after removing a filter | Developers get confused by outdated docs |

---

## Testing

```bash
# Run all SDK tests — MUST pass before committing
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit --filter=ClientTest

# Static analysis — MUST pass before committing
./vendor/bin/phpstan analyse -l 3 src/
```

---

## Validation Commands

```bash
# Check PHP syntax
php -l src/NewFile.php

# Dump autoload after adding classes
composer dump-autoload

# Verify structure
find src -type f -name "*.php" | wc -l  # Should be ~175
```

---

## Current Statistics

- **10** Global resource classes (at `src/`)
- **46** Organization-scoped resource classes (at `src/Organization/`)
- **~65** Service classes
- **9** Exception classes
- **7** Concern traits (`HasRestore`, `HasTagging`, `HasDownload`, `HasImports`, `HasReorder`, `HasIncludes`, `HasFilters`)
- **~175** Total PHP files in src/

---

## Related Documentation

- **API Reference**: https://docs.enlivy.com/api
- **Examples**: See `examples/` directory

---

## Changelog Maintenance

When making changes, update this section:

### Recent Changes
- 2026-02-05: Initial SDK structure with resources and services
- 2026-02-05: Added global Enlivy config singleton
- 2026-02-05: Created examples/ documentation directory
- 2026-02-05: Restructured namespaces - organization-scoped resources moved to `Enlivy\Organization\`, services to `Service\Organization\`
- 2026-02-06: Added HasIncludes concern trait with AVAILABLE_INCLUDES constants and validation on all 56 services
- 2026-02-06: Removed deprecated `curl_close()` calls and fixed Accept header placement in CurlClient
- 2026-02-06: Added HasFilters concern trait with AVAILABLE_FILTERS constants and validation on all 66 list() methods
- 2026-02-06: Fixed all camelCase include keys across 6 SDK services to snake_case
- 2026-02-06: Audited all includes and filters across entire SDK against API — fixed 32 include and 42 filter mismatches
- 2026-02-06: Cleaned stale PHPDoc referencing date range filters on services that don't support them
- 2026-02-06: Rewrote CLAUDE.md — removed internal API implementation details, kept SDK-relevant standards only

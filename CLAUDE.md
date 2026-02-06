# Enlivy PHP SDK - Claude Agent Instructions

> **Self-Maintaining Documentation**: This file and the `examples/` directory must be kept in sync with the Enlivy API. When new features are added to the API, corresponding SDK methods and documentation must be added here.

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

## Self-Maintenance Rules

### Before Making Changes

If you need to understand the API structure, validation rules, or database schema to implement SDK changes properly:

1. **Ask the user** for the path to the Enlivy API codebase
2. Reference the API's controllers, requests, and services to understand:
   - Required vs optional fields
   - Validation rules
   - Relationships between entities
   - Available endpoints and methods

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

3. **Create Service Class**:
   ```php
   // src/Service/Organization/NewEntity/NewEntityService.php
   namespace Enlivy\Service\Organization\NewEntity;

   use Enlivy\Organization\NewEntity;
   use Enlivy\Service\AbstractService;

   class NewEntityService extends AbstractService
   {
       protected const string RESOURCE = 'new-entities';
       // CRUD methods...
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

#### For Global Resources (rare)

1. **Create Resource Class** at `src/NewEntity.php` with namespace `Enlivy`
2. **Create Service Class** at `src/Service/NewEntityService.php` with namespace `Enlivy\Service`
3. Follow same registration steps

6. **Add/Update Documentation** in `examples/`:
   - Create new file if new domain
   - Update existing file if extending feature

### When Modifying Existing Features

1. Update relevant Service class
2. Update Resource class if fields changed
3. Update corresponding `examples/*.md` file
4. Run tests: `./vendor/bin/phpunit`
5. Run PHPStan: `./vendor/bin/phpstan analyse -l 3 src/`

### Documentation Standards

Each `examples/*.md` file should include:
- Overview of the feature
- Prerequisites (what must exist first)
- Code examples with comments
- Common patterns and gotchas
- Related resources/services

---

## Service Patterns

### Standard CRUD Service

```php
class EntityService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'entities';
    protected const ?string RESOURCE_CLASS = Entity::class;

    public const array AVAILABLE_INCLUDES = [
        'relation_a',
        'relation_b',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
        'created_at_from',
        'created_at_to',
    ];

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

    public function create(array $params, ?RequestOptions $opts = null): Entity
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Entity
    {
        $this->validateIncludes($params);
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

| Trait | Methods | Use When |
|-------|---------|----------|
| `HasRestore` | `restore()` | Entity supports soft-delete recovery |
| `HasTagging` | `tag()`, `untag()` | Entity supports tagging |
| `HasDownload` | `download()` | Entity has PDF/file download |
| `HasImports` | `import()`, `importProgress()` | Entity supports bulk import |
| `HasReorder` | `reorder()` | Entity supports manual ordering |
| `HasIncludes` | `validateIncludes()` | Entity supports `?include=` query param |
| `HasFilters` | `validateFilters()` | Entity supports list endpoint filtering |

---

## Testing

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit --filter=ClientTest

# Static analysis
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
find src -type f -name "*.php" | wc -l  # Should be ~173
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

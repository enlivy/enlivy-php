# Data Imports

Load records into an organization from a CSV file. Products, organization users, prospects, bank transactions and billing schedules each expose their own import lane, all sharing one lifecycle and one response shape.

An import is asynchronous: creating one returns a job record immediately and the rows are processed in the background. Poll the job to see how far it got.

## The Lifecycle

```
importDetectColumns()  →  importCreate()  →  importRetrieve()  →  importResume()
   (optional)                                    (poll)           (only if stopped)
```

| Service | Detect columns | Create / list / retrieve | Resume |
|---------|:--------------:|:------------------------:|:------:|
| `$client->products` | ✅ | ✅ | ✅ |
| `$client->organizationUsers` | ✅ | ✅ | ✅ |
| `$client->prospects` | — | ✅ | ✅ |
| `$client->bankTransactions` | — | ✅ | ✅ |
| `$client->billingSchedules` | — | ✅ | — |

The methods only exist where the endpoint does, so calling `importResume()` on billing schedules is a PHP error rather than a 404 at runtime.

## Mapping Columns

Every import addresses columns by **1-based position**, not by header name — `field_position_price => 3` means "price is the third column". If you have the header row, ask the API to propose the mapping instead of building it by hand:

```php
$mapping = $client->products->importDetectColumns([
    'headers' => ['SKU', 'Product Name', 'Unit Price', 'Currency', 'Tags'],
]);

// => field_position_alias: 1, field_position_price: 3, field_position_price_currency: 4, ...
```

Send the headers in file order, including blank ones — position is what the result is built from. Unrecognised headers are simply left out of the mapping, so treat the result as a proposal to show the operator, not a finished configuration.

## Creating an Import

The file goes up as `job_file` (CSV, `text/csv` or `text/plain`), alongside the mapping and the defaults that fill in whatever the file does not carry:

```php
$import = $client->products->importCreate([
    'job_file' => new CURLFile('/path/to/products.csv'),

    // Which rows to read. Row 1 is usually the header.
    'start_from_row' => 2,
    'end_at_row' => 500,          // optional — omit to read to the end

    // Validate and report without writing anything
    'dry_run' => true,

    // Update a product already carrying the same alias instead of adding a second one
    'match_existing' => true,

    // Applied to every row that does not state its own
    'default_type' => 'service',
    'default_currency' => 'EUR',
    'default_primary_currency' => 'EUR',
    'default_organization_tax_class_id' => 'org_tax_xxx',
    'default_is_sold' => true,

    // Column positions
    'field_position_alias' => 1,
    'field_position_price' => 3,
    'field_position_price_currency' => 4,
    'field_position_tags' => 5,

    // Multilingual columns take a locale => position map
    'field_position_name_map' => ['en' => 2, 'ro' => 6],

    // Per-currency price columns
    'field_position_price_map' => ['EUR' => 3, 'USD' => 7],

    // Tags
    'create_missing_tags' => true,
    'tags_separator' => ',',

    // How the file writes decimals — '.' or ','
    'decimal_separator' => ',',
]);

echo $import->id;
```

Start with `dry_run => true`. It runs the same validation and produces the same summary, so you learn what a real run would do before any record exists.

### Product columns

| Parameter | Notes |
|-----------|-------|
| `field_position_alias` | Unique identifier; also the key `match_existing` matches on |
| `field_position_type` | See the product type table in [Products](products.md) |
| `field_position_price` | Single-currency price |
| `field_position_price_currency` | Currency for that price |
| `field_position_price_map` | `{ currency: position }` for per-currency price columns |
| `field_position_primary_currency` | Which currency the product prices in |
| `field_position_tax_class` | Matched against the organization's tax classes |
| `field_position_ean_number`, `field_position_upc_number` | Product codes |
| `field_position_is_sold` | Whether the row is offered for sale |
| `field_position_peppol_billing_unit_code`, `field_position_cpv_code` | E-invoicing codes |
| `field_position_stripe_product_id` | Links the row to an existing Stripe product |
| `field_position_tags` | Split on `tags_separator` |
| `field_position_{name,description,unit,note}_map` | `{ locale: position }` for the multilingual fields |

Each multilingual field also accepts a plain `field_position_{field}` for a single-locale file.

### Organization-user columns

A user import can carry **people and companies in one file**. Give the business rows their own role and the import sorts them as it reads:

```php
$import = $client->organizationUsers->importCreate([
    'job_file' => new CURLFile('/path/to/contacts.csv'),
    'start_from_row' => 2,

    // The role every row gets...
    'default_organization_user_role_id' => 'org_role_person',
    // ...unless the row looks like a company, which needs something to sort by
    'default_business_organization_user_role_id' => 'org_role_company',
    'field_position_organization_type' => 4,

    'match_existing_by_email' => true,
    'default_country_code' => 'RO',
    'default_locale' => 'en',
    'date_format' => 'd/m/Y',

    'field_position_email' => 1,
    'field_position_first_name' => 2,
    'field_position_last_name' => 3,
    'field_position_name' => 5,
    'field_position_address_line_1' => [6, 7],   // several columns joined into one line
    'field_position_address_city' => 8,
    'field_position_address_iso_3166' => 9,
    'field_position_primary_tax_identifier' => 10,
    'field_position_identity_map' => ['vat_number' => 10, 'registration_number' => 11],
]);
```

| Parameter | Notes |
|-----------|-------|
| `default_organization_user_role_id` | Required unless the file names a role per row |
| `default_business_organization_user_role_id` | Must be a business-entity role, and only means something with something to sort rows by |
| `match_existing_by_email` | Update the matching record instead of adding a second one |
| `date_format` | How the file writes dates, e.g. `d/m/Y` — applies to `field_position_birthdate` |
| `field_position_address_line_1` | Accepts a list of positions; the columns are joined |
| `field_position_identity_map` | `{ field: position }` for the country-specific identity fields |
| `field_position_information_map` | `{ field: position }` for the country-specific company fields |
| `determine_missing_iso_3166_with_ai` | Fills in a missing subdivision from the rest of the address; requires the AI add-on |

The identity and information field names are country-specific — read them from `GET /frontend/information-schema/{country_code}`, whose `primary_identifier` names the field that country treats as the main company identifier.

## Watching an Import

```php
$imports = $client->products->importList();

$import = $client->products->importRetrieve($importId);

if ($import->completed_at === null) {
    // still running
}
```

| Field | Meaning |
|-------|---------|
| `id` | Job id — the handle for retrieve and resume |
| `type` | Which lane the job belongs to |
| `settings_json` | The mapping and defaults the job was created with |
| `started_at`, `completed_at` | `null` until the job reaches each point |
| `success` | Whether the run finished without a fatal error |
| `summary_json` | Counters, plus the stop fields below |
| `logs_json` | Per-row outcomes, including why individual rows were rejected |

`logs_json` is left out of `importList()` responses — retrieve a single job to read it.

Rows that fail validation do **not** stop the run — they are recorded in `logs_json` and the import carries on.

## Resuming a Stopped Import

A run that stops early sets three fields in `summary_json`:

| Field | Meaning |
|-------|---------|
| `stop_reason` | Why it stopped — see [Enums](../enums.md) |
| `is_resumable` | Whether a second pass can get further |
| `resume_from_row` | The first row that still needs work |

```php
$import = $client->products->importRetrieve($importId);
$summary = $import->summary_json ?? [];

if (($summary['is_resumable'] ?? false) === true) {
    // Clear the cause first — raise the plan limit, fix the source data —
    // or the new run stops in the same place.
    $resumed = $client->products->importResume($importId);
}
```

Resuming starts a **new job** against the same file, beginning at `resume_from_row`. The original job keeps its own logs and counters, and the new one records which job it continues, so the chain stays readable.

Both preconditions are enforced server-side: a run that completed, or one that stopped for a reason no second pass can clear (an unreadable file), is rejected with a 422.

## Related

- [Products](products.md) — the catalog these rows land in
- [Organization Users](users.md) — customers, employees, and roles
- [Prospects](prospects.md) — pipeline imports
- [Bank Accounts](bank-accounts.md) — statement uploads and Stripe pulls
- [Enums](../enums.md) — import stop reasons

# Changelog

All notable changes to `enlivy/enlivy-php` are documented here. This project
adheres to [Semantic Versioning](https://semver.org/).

## [2.6.0] - 2026-08-10

Blocklists: keep an email, an email domain, or a phone number out of an
organization. Additive throughout.

### Added

- **Blocked identifiers.** `$client->blockedIdentifiers` — list, retrieve,
  create, update, delete against
  `organizations/{org}/blocked-identifiers`. New resource
  `Enlivy\Organization\BlockedIdentifier`. Filters `type` (array) and `source`;
  `organization` include.

  Two lists are enforced together: the platform-wide list Enlivy maintains and
  the organization's own. `source` defaults to the organization's rows; pass
  `all` to see the platform entries alongside them. Platform rows are
  read-only. `value` is validated against the shape `type` implies and must be
  unique within the organization; `normalized_value` is derived server-side and
  is what matching actually runs on, so a number stored as `+40 746 047 047`
  is still found by its digits alone.

- **Check a value without submitting a form.**
  `misc->determineIsEmailBlocked(['value' => ...])` and
  `misc->determineIsPhoneNumberBlocked(['value' => ..., 'country_code' => ...])`
  answer `{ is_blocked, type, source, value, reason }`. Everything but
  `is_blocked` is null when nothing matched. An email can match as itself or by
  its domain — `type` says which rule caught it.

- **`Enlivy\Enums\BlockedIdentifier\Types`** — `email`, `email_domain`,
  `phone_number`. **`Enlivy\Enums\BlockedIdentifier\Sources`** —
  `organization`, `platform`, `all`. `ALL` is a filter directive on the list
  endpoint, not a value a stored row carries.

- **`Organization\SettingGroups`** gains `blocked_identifiers`.

- **New docs:** [Blocked Identifiers](docs/organization/blocked-identifiers.md).

### Changed

- **Blocking is enforced on the inbound surface.** New-user registration and
  customer-portal session creation now reject a blocked email, and registration
  rejects a blocked phone number. These are pass-through endpoints, so no SDK
  signature changes — expect a 422 where a record used to be created. Existing
  records are not affected; adding an entry does not remove them.

- **Prospect-activity event payloads** delivered to event destinations now
  carry `prospect_name` and `prospect_email` flattened onto the activity, so a
  message template can name the prospect without subscribing to the
  `organization_prospect` include.

## [2.5.0] - 2026-08-08

CSV imports for products and organization users, imports that can be resumed
where they stopped, and sandbox organizations to rehearse all of it against.

### Added

- **Sandbox organizations.**
  `$client->organizations->createSandbox('org_xxx', ['name' => '...'])` returns a
  second organization that mirrors the live one's configuration — legal identity,
  locales, currencies, and the whole tax configuration (registrations, filing
  jurisdictions and types, classes with their rates) — but never reaches the
  outside world. Charges, outbound mail and third-party calls fail loudly there
  rather than silently doing nothing. Records are not copied, and connected
  credentials are not inherited. Sandboxes do not nest, and an organization may
  hold only a small number at a time. See [docs/sandboxes.md](docs/sandboxes.md).
- **`environment` on `Organization`** — `live` or `sandbox`, backed by the new
  `Enlivy\Enums\Organization\Environments`.
- **Product imports.** `$client->products` gains the full import surface:
  `importDetectColumns()`, `importCreate()`, `importList()`, `importRetrieve()`
  and `importResume()`. Maps CSV columns by position onto product fields,
  including per-currency price columns and per-locale name/description/unit/note
  columns, with `dry_run` and `match_existing`.
- **Organization-user imports.** The same surface on
  `$client->organizationUsers`, able to carry people and companies in one file:
  give the business rows their own role via
  `default_business_organization_user_role_id` plus something to sort on, and the
  import splits them as it reads.
- **Resumable imports.** `importResume()` on `$client->products`,
  `$client->organizationUsers`, `$client->prospects` and
  `$client->bankTransactions` continues a run that stopped short of the end of
  its file, starting at `summary_json.resume_from_row`. It starts a new job
  against the same file; the original keeps its own logs and counters. Billing
  schedules have no resume endpoint, so the method is deliberately absent there.
- **`Enlivy\Enums\Import\StopReasons`** — `usage_limit`, `ai_limit`,
  `consecutive_failures`, `file_unreadable`. Read `summary_json.is_resumable`
  rather than testing the reason yourself.
- **Column detection.** `importDetectColumns(['headers' => [...]])` on products
  and organization users proposes a `field_position_*` mapping from a CSV header
  row, so an operator can review it before the upload.
- **New docs.** [Data Imports](docs/organization/data-imports.md) documents the
  whole import lifecycle, which had no coverage before this release.
- **`has_full_backoffice_access` on `UserRole`** — a standing grant of every
  ability, present and future, rather than a stored list. Requires
  `can_use_backoffice`; only the organization owner and platform administrators
  may grant it.
- **`sent_cc` on `InvoiceNotificationLog`** — the addresses copied on an invoice
  email, alongside the existing `sent_to`.
- **`subscription_required`** added to `Enlivy\Enums\BillingSchedule\Statuses` —
  a schedule held because the subscription backing it went inactive.
- **`signature_events_log`** on contract-signature create and update: the ordered
  trail of what a signer did while signing, as an array of
  `{event, event_label, timestamp}` entries or an uploaded `txt`/`md`/`json`/`csv`
  file.
- **`use_default_mailer`** on `misc->testEmail()`, and
  `phone_number_country_code` on the user phone update.
- **Invoice writes** accept `organization_bank_account` and
  `organization_receiver_user_address` blocks; update also accepts
  `organization_sender_user` / `organization_receiver_user`.
- **`_action`** is now a declared parameter on file create (`completed`).
- **Three enums that were public but never mirrored** —
  `Enlivy\Enums\Receipt\Directions` (`inbound`, `outbound`),
  `Enlivy\Enums\Receipt\Sources` (`uploaded`, `generated`) and
  `Enlivy\Enums\BillingPackage\PortalDiscoveryMode` (`disabled`, `request`,
  `checkout`). The SDK already handed these values back on `Receipt::$direction`,
  `Receipt::$source` and `BillingPackage::$portal_discovery_mode`; only the
  enums were missing.

### Changed

- **`misc->determineTaxRateId()` renamed its `state` parameter to `iso_3166`**
  (string, max 10) — it takes an ISO 3166-2 subdivision code, which is what the
  rest of the address surface already used. This is a wire break shipped in a
  minor release, deliberately: the endpoint is an undocumented pass-through
  helper with no typed surface, so a major bump would have cost every consumer an
  upgrade for a problem none of them had. Parameters are pass-through arrays, so
  the SDK signature is unchanged — rename the key you send.
- **Clearable address fields.** `address_county`, `address_state` and `timezone`
  now accept `null` on both organizations and organization users, as does
  `address_city` on organization users. Many countries have no county or state
  layer, and requiring one made those addresses unstorable.
- **Tax classes and rates resolve platform ids on retrieve.**
  `taxClasses->retrieve()` and `taxRates->retrieve()` now read back any id the
  matching `list()` handed out, including the platform-wide defaults an
  organization has not overridden. Update and delete are unchanged and still
  resolve only within the organization.
- **Role abilities report what the role answers, not what it stores.**
  `userRoleAbilities->list()` returns the whole ability list for a role holding
  full back-office access, as stand-in entries whose `id` is `null`. Adding
  abilities to such a role is rejected — there would be nothing to read them.
- **Reorder endpoints now validate ownership.** Task, task-status and
  prospect-status reorder reject ids belonging to another organization with a
  422 rather than ignoring them.
- **`convert_to_currency` on billing-schedule analytics is now enforced.** The
  rule behind it never applied before, so a malformed value used to pass through.
- **Prospect-status `is_stuck_threshold_days` must be an integer**, and project
  member `organization_user_id` is now length-checked. Both surface as 422s on
  payloads that previously slipped past.
- **Bank-transaction imports accept only their own types** — statement uploads
  and Stripe charge pulls. Product, user and prospect imports have their own
  endpoints; sending those types to the bank lane is now a 422.

### Fixed

- **`limit` was rejected on every list endpoint.** It is a global filter the API
  accepts everywhere — it caps results on a search (`q`) query — but it was
  missing from `HasFilters::GLOBAL_FILTERS`, so the SDK threw
  `InvalidArgumentException` before the request went out. Now accepted on all
  list endpoints.
- **Thirteen services rejected filters the API accepts.** Each declares the
  filter in the API's own published contract, so passing it was a client-side
  failure only:

  | Service | Filters restored |
  |---------|------------------|
  | `prospectStatuses`, `contractStatuses`, `taskStatuses`, `reportSchemas`, `resourceBundles`, `projects` | `title`, `description` |
  | `taxClasses`, `payslipSchemas`, `products` | `name`, `description` |
  | `bankTransactionCostTypes` | `title` |
  | `bankTransactions` | `is_connected` |
  | `reports` | `reported_by_organization_user_id` |

- **`userRoleAbilities` was unusable.** All three methods were typed against a
  record resource the endpoints never return: they answer with a plain list of
  ability rows (and a status payload on delete). `sync()` and `delete()` raised a
  `TypeError` on every call, and `list()` returned a `Collection` whose `getData()`
  was always empty. All three now return `EnlivyObject` carrying the real payload
  — walk it with `toArray()` or array access. Nothing that worked before changes,
  because nothing worked before.
- **`docs/filters.md` listed `products` as accepting global filters only** — it
  has an `is_sold` filter, now documented in its own section.

## [2.4.0] - 2026-07-29

Scheduled payment reminders, everything that references a contract, consent you
can narrow after the fact, and tax registrations beyond VAT.

### Added

- **Scheduled payment reminders.**
  `$client->invoiceScheduledReminders->list([...])` projects the reminders an
  organization is about to send — `from` / `to` (default 30 days, capped at 366),
  `type`, `organization_invoice_id`. Rows carry the invoice, the reminder type,
  `scheduled_for`, the `sequence` within its type, and enough invoice context
  (`due_at`, `total`, `currency`, `recipient_email`) to render a list without a
  second call. Nothing is stored: rows are recomputed from the current reminder
  settings on every read, so they carry no `id` and move when those settings do.
- **Notification-log filters.** `invoiceNotificationLogs->list()` accepts `types`
  (comma-separated or array) and `created_at_from` / `created_at_to`, so the
  reminders already sent can be read back without client-side filtering.
- **`Enlivy\Enums\Invoice\NotificationLogTypes`** — `network_exchange_auto_push`,
  `email`, `email_auto_send`, `email_reminder_upcoming`, `email_reminder_overdue`.
- **Contract connections.**
  `$client->contracts->connections('org_cont_xxx', [...])` lists every entity
  referencing a contract — proposals, invoices, receipts, payslips, billing
  schedules, scheduled payments and amendment contracts — in one paginated feed,
  narrowable with `entity`. Cancelling a contract deliberately leaves those
  running, so this is what to review before closing them by hand. New resource
  `Enlivy\Organization\ContractConnection`.
- **Narrow an existing OAuth grant.**
  `$client->oauthAuthorizations->update('oauth_cua_xxx', ['scopes' => [...]])`
  drops scopes or organizations from a grant already given. Each list is replaced
  wholesale. Access tokens are re-derived from the authorization record, so a
  removal takes effect at the client's next refresh.
- **Consent can grant less than was asked for.** `oauthAuthorizations->approve()`
  accepts an optional `scopes` list that narrows the grant to a subset of the
  request; omit it to grant everything requested.
- **Tax families.** Tax registrations carry `tax_family` (`vat`, `sales_tax`,
  `income_tax`, `payroll`) — how an organization declares its income-tax or
  payroll standing, which is what makes the corresponding filing obligations
  appear. Defaults to `vat`, and a scheme is validated against its family, so
  `vat_registered` on a payroll row is rejected. New enum
  `Enlivy\Enums\Tax\TaxFamilies`; `Tax\RegistrationSchemes` gains
  `micro_enterprise`, `profit_tax`, `self_employed_income`, `employer`.
- **`Organization.customer_portal_base_url`.** The base URL customers actually
  land on, honoring a verified custom domain. Read it rather than hardcoding a
  host — the fallback lives in server configuration.
- **Filing is opt-in.** `Tax\AssuranceModes` gains `none` — the affirmative
  "filed elsewhere", distinct from never having been asked. Holding a tax
  registration no longer conscripts an organization into generated filing periods.
- **Enum cases.** `Organization\SettingGroups` + `invoice_payment_reminder`;
  `Organization\EntityManifest` + `billing_scheduled_payment`.

### Changed

- **Invoice log endpoints moved under `invoices/`.** `invoice-charge-logs` →
  `invoices/charge-logs` and `invoice-notification-logs` →
  `invoices/notification-logs`. The SDK's PHP surface is unchanged —
  `$client->invoiceChargeLogs` and `$client->invoiceNotificationLogs` keep their
  names, methods and signatures — only the emitted paths differ. **This is a wire
  break that was deliberately not treated as breaking**: these are secondary
  read-only endpoints with no traffic at the time of the move, so the cost of a
  major bump outweighed the risk. Pin `2.3.x` if you are calling the old paths
  directly rather than through the SDK.
- **OAuth consent endpoints are first-party only.** `/oauth/authorize/*` and
  `/oauth/authorizations/*` no longer accept an OAuth access token — authenticate
  with an API key. An access token approving its own consent could widen its own
  grant.
- **Portal contract abilities are state-aware.** A terminated contract no longer
  offers `sign`, and `download` follows the signed-document evidence rather than a
  timestamp.
- **Person-name fields and password length are validated on write.** `first_name`
  and `last_name` across registration, users, prospects and contract parties now
  reject non-name input; registration passwords are length-bounded. Previously
  accepted payloads may now return 422.

### Fixed

- **Calendar dates are no longer sent as timestamps.** Columns that hold a
  calendar day now serialize as `Y-m-d` instead of a midnight-UTC instant — which
  rendered as the previous day in any negative-offset timezone. Affects
  `TaxRegistration.cash_accounting_from` / `cash_accounting_to` / `effective_from`
  / `effective_to`, `TaxEvent.tax_point_date` / `document_date`,
  `TaxFilingPeriod.period_start` / `period_end` / `filing_due_date` /
  `payment_due_date`, `TaxFilingPeriodPayment.payment_date`,
  `Report.report_date`, `OrganizationUser.birthdate` and payment-method
  `expires_at`. This is the format the docs and `/discovery` already declared.
- **`oauthAuthorizations` returns typed objects.** `list()` and `revoke()`
  declared `OAuthAuthorization` while the service had no resource class, so
  `revoke()` raised a `TypeError` at runtime. The consent endpoints (`info()`,
  `approve()`, `deny()`) keep returning raw objects.
- **`InvoiceNotificationLog` fields corrected.** The resource advertised five
  properties the API never returns (`organization_id`, `recipient_email`,
  `status`, `sent_at`, `error_message`) and omitted two it does: `sent_to` (the
  recipient address) and `message`. Annotations only — no runtime behaviour
  changes — but `$log->recipient_email` was always null; read `$log->sent_to`.
- **Resource fields re-derived from the API across the whole SDK.** Every
  resource's `@property` block was regenerated from the response it actually
  receives, in response order. 52 resources changed. The pattern throughout:
  properties that were never returned (`Invoice.status_color`,
  `Invoice.payment_stripe_*`, `Receipt.deleted_by_user_id`,
  `Contract.formatted_number`, `File.disk_path`, `ApiCredential.credentials`, …)
  and returned fields that were missing (`Invoice.will_number_be_auto_assigned`,
  `BankAccount.sync_provider`, `ContractSignature.has_signature_image`,
  `Notification.title`, `Prospect.state_disqualified_reason`, …).
  `InvoiceNotificationLog` above is one instance. Annotations only — no runtime
  behaviour changes — but a property that was never returned always read null.
- **`SettingService::update()` could not work.** It posted to
  `/settings` while the endpoint is `POST /settings/{key}`, and took no key at
  all. Now `update(string $key, array $params)`. Same fix on
  `UserOrganizationSettingService::update()` and `::delete()`, which each gain a
  `$key` after `$userId`/`$orgId`.
- **`billingSchedules->addTag()` / `removeTag()` removed.** Billing schedules
  have no tagging endpoints; both methods targeted a path that does not exist.
  (The `tag_ids` include and filter are unrelated and still work.)
- **`Contract\StateActionRequiredTypes` phantom cases removed.**
  `RECEIVER_RECEIPT_CONFIRMATION_REQUIRED`, `SENDER_SIGNATURE_REQUIRED` and
  `SENDER_RECEIPT_CONFIRMATION_REQUIRED` were mirrored from a commented-out block
  upstream and were never real values. `PARTIES_SIGNATURES_REQUIRED` is the only
  case the API has ever emitted.

## [2.3.0] - 2026-07-23

Create a billing schedule directly from a subscription package (with an optional
immediate first charge), a tax-applicability check, and access to the response
metadata that rides alongside a created resource.

### Added

- **Billing schedule from a billing package.**
  `$client->billingSchedules->fromBillingPackage([...])` materializes a
  subscription schedule straight from a package — pass
  `organization_billing_package_id`, an optional
  `organization_billing_package_subscription_term_id` (cadence variant),
  optional `selected_group_items`, and `start_at` (omit or `null` = start now).
  When the schedule is created `active` and already due, the first cycle is
  invoiced and charged inline.
- **Inline first-charge result.** A created schedule carries the charge outcome
  on the response meta — `meta.charge_result` (`status` /
  `error_code` / `error_message` / `provider_reference` / `next_action_url`) and
  `meta.invoice_id`. Reachable via the new `lastResponse()` accessor (below).
  Applies to both `fromBillingPackage()` and `create()`.
- **`EnlivyObject::lastResponse()`.** Every object returned by the SDK now
  carries the raw `ApiResponse` it was hydrated from — status code, headers, and
  the decoded body (including any `meta`). Read endpoint metadata (or a response
  header) with `$object->lastResponse()?->json['meta']`.
- **Tax applicability check.**
  `$client->misc->determineIsTaxCharged([...])` reports whether a sale will carry
  tax for a recipient — by `organization_receiver_user_id`, or ad-hoc via
  `country_code` / `is_business_entity` / `is_eu_vat_registered`. Returns
  `is_tax_charged`, `reason`, `needs_attention`. New enum
  `Enlivy\Enums\Tax\TaxApplicabilityReasons`.

### Changed

- **Breaking (wire): billing-package fields are rejected on
  `billingSchedules->create()`.** Package-backed creation now lives solely on
  `fromBillingPackage()`. Sending `organization_billing_package_id`,
  `organization_billing_package_subscription_term_id`, `selected_group_items` or
  `start_at` to `create()` is rejected — the endpoint composes explicit
  `phases`/`payments` only. The SDK's PHP surface is unchanged (`create()` keeps
  its signature); see [UPGRADING](UPGRADING.md) to migrate.

## [2.2.0] - 2026-07-20

E-invoicing status with a filing preview, plus a selectable document type when
pushing an invoice to a tax-authority network.

### Added

- **E-invoicing status & document-type override.**
  `$client->invoices->peppolStatus($id, $institution)` returns an invoice's
  status on a tax-authority e-invoicing network, with a filing preview when it
  has not yet been pushed (ANAF only). Both `peppolStatus()` and `peppolPush()`
  accept an optional `document_type_code` (`380` commercial invoice, `381`
  credit note). New enum `Enlivy\Enums\NetworkExchange\DocumentTypeCodes`.

## [2.1.0] - 2026-07-20

Payment-method binding, organization integration keys, a payment-provider naming
alignment, and a foreign-currency conversion fix.

### Added

- **Bind a payment method.** `$client->userPaymentMethods->bind($userId, [...])`
  attaches an externally-tokenized payment method (e.g. a Stripe PaymentMethod
  created off-platform with the organization's publishable key) to a user — pass
  `payment_provider` (`stripe`) and `stripe_payment_method_id`.
- **Organization integration keys.** The `Organization` resource now exposes an
  `integrations` map carrying the public keys an off-platform integration needs
  (`integrations.stripe.publishable_key`, `integrations.stripe.account_id`), or
  `null` for a provider that is not connected.
- **Enum cases.** `Payment\PaymentMethodOrigin` gains `api_bind`;
  `Tax\RegistrationSuggestionConfidences` gains `derived`;
  `Tax\RegistrationSuggestionSources` gains `activity`.

### Changed

- **Payment-provider naming.** The `Payment\PaymentMethodProvider` enum is now
  `Payment\PaymentProvider` (values unchanged: `stripe`, `paypal`), matching the
  API. The user-payment-method `provider` field follows suit and is now
  `payment_provider` — on the `UserPaymentMethod` resource, the `create()` body,
  and the `list()` filter. To migrate, swap
  `Enlivy\Enums\Payment\PaymentMethodProvider` for
  `Enlivy\Enums\Payment\PaymentProvider` and the `provider` key for
  `payment_provider`.

### Fixed

- `BankAccountBalance.balance_converted_currency` is now `null` when no exchange
  rate is available, instead of a misleading `0.0` — part of an upstream
  foreign-currency conversion fix across balances, analytics, and forecasts.

## [2.0.0] - 2026-07-19

A tax-compliance engine plus invoice refunds and proposal-to-billing-schedule
support. The bump to `2.0.0` reflects wire-contract removals and renames (see
[UPGRADING.md](UPGRADING.md)); the SDK's PHP surface stays source-compatible —
no class, method, or enum case was removed.

### Added

- **Tax-compliance subsystem.** New services `$client->taxRegistrations`,
  `$client->taxEvents`, `$client->taxFilingPeriods`, and (nested)
  `$client->taxFilingPeriodPayments`, with resources `TaxRegistration`,
  `TaxEvent`, `TaxFilingPeriod`, and `TaxFilingPeriodPayment`. Registrations add
  `suggested()`; filing periods add `acceptComputed()` and `returnView()`; all
  support soft delete and `restore()`. Filing-period payments are nested — every
  method takes the filing-period id first. See
  [docs/organization/taxes.md](docs/organization/taxes.md).
- **Invoice refunds & issuance.** `$client->invoices->refund()` (full or partial,
  generating a reversal/credit-note invoice), `issueInvoice()` (mint a standard
  invoice from a proforma), and `issueReceipt()`. Two new invoice includes:
  `reversal_invoices` and `parent_invoice`.
- **Manual proposal → billing schedule.** `$client->proposals->createBillingSchedule()`
  for an accepted subscription proposal (gated on the new read-only
  `can_create_billing_schedule` flag). Proposals also expose
  `has_unsigned_required_contracts` and `billed_currency`.
- **Billing-package contract preview.** `$client->billingPackages->previewContractTemplate($id, $templateId)`
  returns a rendered `Contract`. Contract-template sections gain `content_source`
  (enum `BillingPackage\ContractSectionContentSources`) and `configuration`;
  templates gain per-party `sender_rawd_lang_map` / `receiver_rawd_lang_map`.
- **Data export.** `ExportData` gains `type` / `parameters` and the export
  service a `type` filter — `accounting_saga` exports take
  `parameters.date_from` / `date_to`.
- **Discovery & monitors.** `$client->organizations->discovery($id)` (org-scoped
  discovery) and `$client->misc->taxMonitors()`.
- **Setting localizations.** `$client->settingLocalizations` — `list()`,
  `retrieve($group, $key)`, `set($group, $key, ...)`, `delete($group, $key)`.
- **Enums.** The `Enlivy\Enums\Tax\*` family (product categories, registration
  schemes, seller VAT statuses, validation/suggestion sources, filing
  frequencies/statuses, payment types/statuses, assurance modes, and tax-event
  directions/source-types/regimes/supply-types), plus `Payment\RefundStatus`,
  `CurrencyExchangeRateProviders`, `BillingPackage\ContractSectionContentSources`,
  `ExportData\Types`, and `Organization\SettingGroups`. New cases:
  `BillingSchedule\Statuses` (`cancelling`), `EventTrail\EventType` (`refunded`,
  `refund_failed`), `TenantBilling\PackStatuses` (`active_cancelled`).
- **Resource fields.** `TaxClass` gains `display_name` / `display_name_lang_map`
  / `tax_category`; `TaxRate` gains localizable `name` / `display_name`,
  `retired_at` / `retired_reason_lang_map` / `retired_by_user_id`,
  `stripe_tax_rate_id`, and `auto_imported_from` / `auto_imported_hash`.
- **Automatic retries.** Transient failures (connection errors, `429`, `5xx`)
  are retried with exponential backoff and `Retry-After` support — for `GET`
  requests, and for writes carrying an `Idempotency-Key`. The `max_retries`
  client option and `Enlivy::setMaxNetworkRetries()` now take effect
  (previously accepted but ignored).
- **Auto-pagination.** `Collection::autoPagingIterator()` lazily walks every
  page: `foreach ($client->invoices->list() as ...)` stays single-page,
  `foreach ($collection->autoPagingIterator() as ...)` iterates them all.
- **Request options.** `RequestOptions` gains per-request `timeout` and extra
  `headers`. Client telemetry (`X-Enlivy-Client-User-Agent`: SDK/PHP/OS
  versions) is sent by default; disable with `Enlivy::setEnableTelemetry(false)`.
- `ApiResource::isDeleted()` — true when the resource carries a `deleted_at`.
- CI (GitHub Actions, PHP 8.3–8.5), `CONTRIBUTING.md`, and `SECURITY.md`.

### Changed

- `TaxRate.country_code` is now `seller_country_code` (system-managed). Tax-rate
  locations take `country_code` and `zip_code`.
- The subscription cadence field on a proposal created from a package (and on a
  Client Portal claim) is now `organization_billing_package_subscription_term_id`
  (previously `subscription_term_id`).
- PHPStan gate raised to level 5 with zero errors.

### Fixed

- Raw downloads (`download()` methods) now throw the typed `ApiException`
  hierarchy on HTTP errors instead of returning the error JSON as if it were
  file content.
- `Enlivy::setVerifySslCerts()` / `setCaBundlePath()` are now honored by the
  cURL transport (previously silent no-ops).

### Removed

- **`Product.price_is_tax_inclusive`** — tax treatment now derives from the
  product's assigned tax class / category.
- **`TaxRate.is_shipping`**, and the `iso_3166` write field on tax-rate locations.
- The stale `TaxClass.alias` property (never emitted by the API).
- `Enlivy\Util\Util::flattenParams()` (unused).

## [1.1.0] - 2026-06-29

Subscription cycle-length support and customer self-service subscription
management, plus assorted resource/field corrections. All additive.

### Added

- **Subscription cadence variants.** A `subscription` billing package can offer
  one or more cadence variants (e.g. Monthly, Annual), each with its own
  frequency, currency, and per-item pricing. New resources
  `Enlivy\Organization\BillingPackageSubscriptionTerm` and
  `BillingPackageSubscriptionTermItem` — author them inline on the package via
  `subscription_terms[]` and read them back through the `subscription_terms`
  include. The chosen variant flows as
  `organization_billing_package_subscription_term_id` on a proposal created from a
  package, on a Client Portal claim, and on the resulting billing schedule (with a
  `subscription_term` include on the proposal and billing-schedule services). See
  [docs/organization/billing-packages.md](docs/organization/billing-packages.md).
- **Customer self-service subscriptions.** The Client Portal billing-schedule
  service gains `reconfigure()`, `previewReconfigure()` (returns the
  proration/charge preview), `pause()`, and `resume()`. The organization
  billing-schedule service gains `reconfigure()` and `previewReconfigure()` (the
  admin lane, which also accepts `subscription_term_id` to switch cadence), plus
  `status_not` and `organization_user_id` filters and a `subscription_term`
  include.
- **Enums.** `BillingPackage\SubscriptionTermStatuses` (`active`, `archived`) and
  `BillingPackage\BillingEffect` (`now`, `next_cycle`); `BillingSchedule\PhaseFrequency`
  gains `every_3_months` and `every_6_months`.
- New resource fields: `Receipt` (`source`, `finalized_at`, `has_file`),
  `ReceiptPrefix` (`description`, `reset_yearly`, `counter_year`,
  `formatted_number`), and a `receipt_prefix` include on the receipt service.

### Changed

- `BillingPackage` exposes `available_currencies` (replacing the no-longer-emitted
  `currency` / `currency_list`) and the `customer_can_reconfigure` /
  `customer_can_cancel` / `customer_can_pause` capability flags.
- `BillingSchedule` now carries `organization_billing_package_id`,
  `organization_billing_package_subscription_term_id`,
  `organization_user_payment_method_id`, `management_type`,
  `payment_provider_billing_reference`, and the `customer_can_*` flags; the
  no-longer-emitted `type`, `frequency`, `formatted_total`,
  `payment_stripe_account_id`, and `payment_stripe_subscription_id` properties were
  removed.
- `Proposal` references a package via `organization_billing_package_id` /
  `organization_billing_package_payment_plan_id` (renamed from the legacy
  `organization_offer_*`) and adds the
  `organization_billing_package_subscription_term_id` and
  `organization_billing_schedule_id` links.
- `OAuthToken::$expires_at` is typed `int|null` (a Unix timestamp), correcting the
  previous `string|null`.
- `InvoiceNetworkExchange` property list corrected to the institution/exchange
  fields the API returns.

### Fixed

- `BillingSchedule\Statuses` now includes `payment_method_required` and `paused`,
  which were missing and broke typed hydration of paused subscriptions.

## [1.0.0] - 2026-06-12

First stable release. See [UPGRADING.md](UPGRADING.md) for migration steps from
the `0.x` series.

### Breaking

- **Webhooks are now Event Destinations.** The webhook management API has been
  replaced by a unified event-delivery system. `$client->webhooks` becomes
  `$client->eventDestinations`, and endpoints move from `/webhooks` to
  `/event-destinations`. A destination has a `type` (`webhook` or `slack`), a
  `destination_url`, an optional `name`/`config`, and one or more
  `event_subscriptions`. Webhook delivery payloads and **signature verification
  are unchanged** — `Enlivy\Webhook\WebhookSignature` and
  `Enlivy\Webhook\WebhookEvent` still work exactly as before.
- **Tenant-billing trial.** `tenantBillingTrial->activate()`, `addPack()` and
  `dropPack()` are replaced by a single
  `tenantBillingTrial->applyChangeSet($params)` call.
- **`Enlivy\Enums\Proposal\PaymentMethodKind`** — the `SAVED_CARD` case
  (`'saved_card'`) is now `CARD` (`'card'`), matching the API wire value.

### Added

- **Event Trails** — read-only audit history exposed on invoices, receipts and
  billing schedules via `eventTrails()` / `retrieveEventTrail()`, with the
  `EventTrail` / `EventTrailChange` resources and `EventTrail\{EventType,
  Origin}` enums.
- **Event Destinations** — subscriptions and delivery logs through
  `subscriptions()`, `deliveries()` and `retrieveDelivery()`, plus Slack as a
  destination type; `EventDestination` / `EventDelivery` / `EventSubscription`
  resources and `EventDelivery\{DestinationType, DeliveryStatus, TriggerEvent}`
  enums.
- **Slack integration** — `serviceIntegrationSlack->connect()`.
- **Client Portal** — `payslips`, `billingSchedules` (incl.
  `changePaymentMethod()` / `cancel()`), magic-authentication session selection
  (`session->candidateUsers()` / `bindUser()`), and proposal
  `createPaymentIntent()` / `confirmPayment()` (re-introduced).
- **Tax classes** — `tax_rates_overview` include.
- **Invoice network exchanges** — `created_at_from`/`created_at_to` and
  `updated_at_from`/`updated_at_to` filters.
- **Portal sessions** — list filters `organization_user_id`, `status`,
  `created_at_from`/`created_at_to`, `updated_at_from`/`updated_at_to`.
- New resource fields, including `BillingPackage.portal_url` /
  `portal_discovery_mode`, `BillingSchedule` cancellation and
  email-notification fields, `Project` inbound-prospect fields, and
  `TenantBilling` trial-window fields.
- New enums `Organization\ButtonStyles` and
  `TenantBilling\{BillingEffects, TrialChangeSetTypes}`.

### Changed

- Added enum cases: `Organization\EntityManifest`
  (`billing_package`, `proposal`, `file`), `Proposal\Statuses`
  (`lead_configured`), `TenantBilling\PackStatuses` (`trialing_cancelled`),
  `UserClientPortal\SessionStatuses` (`awaiting_user_selection`).

## [0.2.0] - 2026-05-18

### Added

- **Tenant Billing** — `tenantBilling` (catalog, state, terms, usage, preview,
  apply), `tenantBillingTrial`, `tenantBillingPaymentMethods`, and
  `tenantBillingInvoices` services, plus the `TenantBilling` resource.
- **Invoice charging** — `invoices->charge()`, the `InvoiceChargeLog` resource
  with a read-only `invoiceChargeLogs` service, and the `charge_logs` /
  `latest_charge_log` includes on invoices.
- **Organization-user payment methods & bank accounts** — `userPaymentMethods`
  (incl. import/sync from Stripe, set-as-default) and `userBankAccounts`
  (incl. set-primary) services with `UserPaymentMethod` / `UserBankAccount`
  resources; `primary_bank_account` and `bank_accounts` includes on
  organization users.
- **Client Portal** — `paymentMethods` service and `invoices->charge()`.
- **Enums** — 69 string-backed enum classes under `Enlivy\Enums\…` mirroring
  the API value sets, with an `EnumValues` helper trait
  (`values()`/`names()`/`isValid()`). See `docs/enums.md`.
- `billingPackages->download()` and the `subscription_terms` include.
- `misc->calculateCurrencyConversion()` and `misc->calculateDueDate()`.
- `serviceIntegrationStripe->detectedCurrencies()` and `createBankAccount()`.
- `taxFilingJurisdictions->create()`.
- `frontend->sockets()`.
- `bank_account_data_bridge` include on bank accounts.
- New documentation: tenant billing, enums; expanded invoice & user docs.

### Changed

- `proposals->attachContract()` now posts to `…/{id}/contracts`.
- `bankAccountData->getRequisition()` path corrected.
- Invoice network-exchange `update()` now uses `PATCH`; its `restore()` path
  corrected.

### Removed

- **Breaking:** the legacy `membership` service has been removed. Use the
  `tenantBilling*` services instead.
- Removed methods that no longer have a corresponding endpoint: analytics
  billing-documents, portal proposal create-payment-intent / confirm-payment,
  invoice network-exchange `create()`, and the client-portal session
  `delete()` (use `expire()`).

### Fixed

- `src/Enlivy.php` `VERSION` constant now reflects the released version.

## [0.1.0] and earlier

See the Git tag history (`0.0.1`–`0.1.0`).

# Changelog

All notable changes to `enlivy/enlivy-php` are documented here. This project
adheres to [Semantic Versioning](https://semver.org/).

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

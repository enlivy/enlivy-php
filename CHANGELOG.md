# Changelog

All notable changes to `enlivy/enlivy-php` are documented here. This project
adheres to [Semantic Versioning](https://semver.org/).

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

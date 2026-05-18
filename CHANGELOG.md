# Changelog

All notable changes to `enlivy/enlivy-php` are documented here. This project
adheres to [Semantic Versioning](https://semver.org/) (pre-1.0: minor versions
may contain breaking changes).

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

# Changelog

All notable changes to `enlivy/enlivy-php` are documented here. This project
adheres to [Semantic Versioning](https://semver.org/).

## [3.4.0] - 2026-10-01

Packages sell through checkout sessions, an accepted proposal is paid in one call, and an open
invoice can be paid from an emailed link without signing in.
Scoped as a minor: the two removed methods called routes the API had already retired.
New docs: [Checkout Sessions](docs/organization/checkout-sessions.md). See [UPGRADING](UPGRADING.md).

### Removed

- Portal `proposals->selectPaymentMethod()` and `proposals->createPaymentIntent()`: the API replaced both routes with `proposals->pay()`.

### Added

- `checkoutSessions` (list/retrieve/create/expire) and the `CheckoutSession` resource; `create()` honours `RequestOptions::$idempotencyKey`, and the browser's token is on the response meta as `client_token`.
- `checkoutSessions->update()`: corrects the reference, campaign fields and metadata, and moves an open session's expiry later.
- `Enlivy\Embed\CheckoutEmbed`: renders the checkout embed for a session's client token, escaped for an inline script, with an optional CSP nonce.
- `examples/checkout` (in the repository, left out of the Composer package): a runnable site that sells a package end to end against a sandbox organization.
- `misc->calculateBillingPackagePrice()`: what a checkout session would charge, priced as one and never saved.
- `invoices->sendPaymentLink()`: email the customer a link to pay an open invoice without signing in.
- Portal `proposals->pay()` (card or bank transfer) and `billingPackages->openCheckoutSession()`.
- `Proposal::$payment_method_kind`.
- Enums `CheckoutSession\Statuses`, `CheckoutSession\PaymentStatuses`, `CheckoutSession\Modes`, `Payment\AttentionIssue`.
- Enum cases: `UserClientPortal\SessionPermissions` (`working_time`, `payslips`, `knowledge`, `billing_schedules`, `projects`, `prospects`, `checkout`), `EventDelivery\TriggerEvent` (`checkout_session.completed`, `checkout_session.expired`), `EventTrail\EventType` (`needs_attention`).

### Changed

- A charge whose `charge_result.status` is `requires_action` now answers a `next_action_url` that is a payment link already emailed to the customer, and automatic retries wait for it with `error_code` `awaiting_customer_action`. A tenant charge needing approval also answers `meta.payment` for Stripe.js.
- A card proposal's invoice is issued with its payment, not at acceptance.
- An OAuth access token reaches only the organizations it was granted, its own grant and the organization list.
- Portal `billingPackages->claim()` returns the claimed `Proposal`, with typed params; it is being retired in favour of `openCheckoutSession()`.
- A portal session created with `permissions` now reaches only what it lists; one created without them reaches everything. The profile, proposals, the package catalogue and session selection stay open to every session.
- A payment plan phase without `max_occurrences` bills until the schedule is cancelled.
- Deleting a prospect stage also deletes the paths into and out of it, and restoring the stage brings them back; a path cannot lead to a deleted stage.

### Fixed

- The invoice email examples sent `to`, `cc` and `subject`, which the API does not read; it takes `send_to`, `message`, `locale` and `type`.
- `organizations->retrieve()`, `create()`, `update()`, `delete()`, `restore()` and `createSandbox()` threw a `TypeError`; they return `Organization`.
- The portal proposal example built the client with `session_token`; the key is `portal_token`.
- The `prospects->advance()` example left out the required `organization_prospect_stage_path_id` and sent a `note` the API does not read.

## [3.3.0] - 2026-09-27

Tasks are rebuilt on typed board stages, with assignees and followers, links, comments, a feed
and email reminders. The helpdesk learns to hold mail from strangers and to file what it did not
open; prospects carry their links and a place on the board.

Scoped as a minor despite the removals below, matching the 3.1.0 and 3.2.0 precedent.
New docs: [Tasks](docs/organization/tasks.md). See [UPGRADING](UPGRADING.md).

### Removed

- `$client->taskStatuses`, `Organization\TaskStatus` and `TaskStatusService` — replaced by task stages.
- `tasks->reorder()` — tasks are placed on the board with `moveOnBoard()`.
- `Task` fields `assigned_by_organization_user_id`, `assigned_to_organization_user_id`, `organization_task_status_id`, `organization_report_schema_id`, `organization_report_id`, `title_lang_map`, `content_lang_map` and `order`, with their includes and the `assigned_*` and `has_lang_map` filters.
- `Prospect::$social_profiles` — replaced by `links`.
- `Organization\EntityManifest::TASK_STATUS` — now `TASK_STAGE`.

### Added

- `taskStages` (list/retrieve/create/update/delete/restore/reorder) and `taskComments` (create/update/delete).
- `tasks`: `board()`, `complete()`, `cancel()`, `reopen()`, `moveOnBoard()`, `follow()`, `unfollow()`, `feed()`, `notifications()`, `eventTrails()`, `retrieveEventTrail()`.
- `Task` fields `title`, `content`, `status`, `status_changed_at`, `board_rank`, `origin`, `organization_task_stage_id`, `created_by_organization_user_id`, `cancelled_at`, `cancelled_by_organization_user_id`, `cancel_reason` and the subtask and comment counts; seven new includes and fourteen filters.
- Resources `TaskStage`, `TaskParticipant`, `TaskBoardColumn`, `TaskFeedEntry`, `Comment`, `NotificationSubject`.
- `organization_tasks` include on invoices and prospects.
- `prospects->moveOnBoard()`, `Prospect::$board_rank` and `Prospect::$links`.
- Helpdesk conversations: `markUnread()`, `blockSender()`, filters `assignment` and `has_unread`, include `lifecycle`, fields `last_message_type` and `last_message_preview`.
- Helpdesk inbound email: `promote()`, `classify()`, `blockSender()`, `fetchOriginal()`, filter `category`, includes `headers`, `trust_assessment` and `blocked_identifier`, fields `category`, `trust_score`, `trust_symbols`, `content_type` and `organization_blocked_identifier_id`.
- Helpdesk inboxes: `is_widget_enabled`, `is_widget_origin_restricted`, `is_quarantine_enabled`, `quarantine_score_threshold`, `auto_response_chat_delay_seconds` and the open and pending counts; settings include `inbox_defaults`.
- `ApiCredential` fields `is_active_sender`, `receives`, `synced_since_at`, `last_synced_at`; `Notification::$sent_by_user_id` with the `sent_by_user` and `subjects` includes.
- Enums `Task\TaskStatuses`, `Task\TaskCancelReasons`, `Task\TaskOrigins`, `Task\TaskParticipantRoles`, `Task\TaskParticipantSources`, `Helpdesk\InboundEmailCategories`, `WebLinkKinds`.
- Enum cases: `EventDelivery\TriggerEvent` (10 helpdesk events), `Helpdesk\InboundEmailInterpretations` (`bulk`, `quarantined`, `own_address`), `Helpdesk\InboundEmailRuleActions` (`trust`), `Helpdesk\MessageContentTypes` (`unsnoozed`, `transcript`), `Tax\MappingSuggestionBases` (`foreign_jurisdiction`), `UserClientPortal\SessionPermissions` (`helpdesk`).

### Changed

- `tasks->delete()` returns the status envelope as an `EnlivyObject` rather than a `Task`.
- Widget embedding is governed by `is_widget_enabled` and `is_widget_origin_restricted`; `widget_origins` applies only while restricted.
- Inboxes created through the API hold mail from unknown senders unless `is_quarantine_enabled` is sent false.
- Credential writes merge: keys sent are set, keys sent empty are removed, keys left out are kept.
- `misc->testEmail()` takes `organization_api_credential_id`, no longer requires `send_to`, and reports `send` and `receive` checks.
- `HelpdeskConversation::$number` is typed `int|null`; the API no longer guarantees one.
- The `tasks` feature pack is retired; tasks are free.

### Fixed

- `include_meta` was rejected by client-side filter validation on every list; it now passes through.
- The tax-mapping docs read a top-level `outcome` the API never sent; each treatment carries a `suggestion`.
- Stage colour examples used an `rgba(…)` form the API refuses; it takes `r, g, b, a` numbers. Two field tables (prospects, users) rendered partly as raw text.

## [3.2.0] - 2026-09-15

A support desk: inboxes, conversations, teammates, inbound mail, visitors and proactive messages,
on both the staff lane and the customer's own. Alongside it, a statement archive on bank accounts,
retiring a tax class instead of deleting what names it, and a unified connections feed.

Scoped as a minor despite the removals below, matching the 2.1.0 and 2.7.0 precedent.
New docs: [Helpdesk](docs/organization/helpdesk.md), [Embedded Support](docs/embedded-support.md).
See [UPGRADING](UPGRADING.md).

### Removed

- `$client->payslipSchemas`, `Organization\PayslipSchema`, `Enums\Payslip\Fields` — the API retired payslip schemas; payslip data moved to typed columns and coded lines.
- `Organization\ContractConnection` — replaced by `Organization\Connection`.
- `$client->projectPermissionProspects` — replaced by `$client->projectPermissionPipelines`.
- `$portal->prospects->board()` — the API withdrew the project-wide board; use `$portal->pipelines->board()`.
- `Payslip::$organization_payslip_schema_id`, `Payslip::$information`, the `organization_payslip_schema` include and the `organization_payslip_schema_id` filter.

### Added

- **Helpdesk, staff lane**: `helpdeskConversations`, `helpdeskConversationMessages`, `helpdeskConversationAttachments`, `helpdeskConversationParticipants`, `helpdeskInboxes`, `helpdeskTeammates`, `helpdeskSettings`, `helpdeskInboundEmails`, `helpdeskInboundEmailRules`, `helpdeskProactiveMessages`, `helpdeskVisitors`.
- **Helpdesk, customer lane**: `$portal->helpdeskConversations`, `$portal->helpdeskAttachments`.
- 14 helpdesk resource classes and 15 helpdesk enums.
- `bankAccountStatements` (list/create/download/delete/restore) and `bankAccounts->downloadStatementsForMonth()`.
- `taxClasses->retire()`, `taxClasses->unretire()`, `taxClasses->connections()`, `taxRates->connections()`, plus the `retired` filter on `taxClasses`.
- `misc->determineTaxClassId()`, `prospects->download()`, `eventDestinations->test()`, `receipts->pdf()`.
- `frontend->timezones()`, `frontend->informationSchema()`, `frontend->supportWidgetToken()`, `invitationCodes->retrieve()`, `settings->retrieveForUser()`.
- `serviceIntegrations->slackChannels()`, `->slackTest()`, `->gmailConnect()`.
- Portal: `pipelines`, `prospectActivities`, `projectMembers`, plus `prospects->listAcrossProjects()`, `->retrieveAcrossProjects()` and `->advance()`.
- `createForUser()` on the project and resource-bundle permission services, and `projectProspectStages->createForStage()`.
- `Collection::getMeta()` — reads the response `meta` block, which is where connection facet counts live.
- Enum cases: `TenantBilling\FeaturePacks` (`payroll`, `helpdesk`), `CapacityAddons` (`helpdesk_seats`), `MeteredDomains` (`employment`, `helpdesk_seat`), `EventDelivery\TriggerEvent` (2 restore events + 3 helpdesk events), `Organization\EntityManifest` (6 helpdesk entities).
- Enums `BankAccount\StatementFormats`, `Organization\ConnectionLiveness`, `Project\ProspectAccessScopes`.
- Resource fields: `ApiCredential::$organization_user_id`/`$is_default`, `TaxClass::$retired_at`/`$retired_reason_lang_map`/`$retired_by_user_id`, `ProspectActivity::$from_assigned_organization_user_id`/`$to_assigned_organization_user_id`.

### Changed

- `contracts->connections()` returns `Collection<Connection>`; rows are now `{id, entity, liveness}` with the referencing entity under `item`. It also accepts `liveness`.
- `Organization\EntityManifest` no longer has `payslip_schema`.

### Fixed

- Portal session expiry was documented as capping at 7 days; it is 365.
- The portal session permission list omitted `payment_methods`.

## [3.1.0] - 2026-09-03

The prospect *status* is now the prospect *stage*, everywhere — two classes, two
accessors, one enum and a dozen wire fields renamed together, with no aliases.
Scoped as a minor because nothing changed but names. New alongside it: a payroll
lane (employments, working time, payslips with typed lines), prospect pipelines,
and duplicate detection with merge. See [UPGRADING](UPGRADING.md).

### Changed

- `$client->prospectStatuses` → `$client->prospectStages`; `$client->projectProspectStatuses` → `$client->projectProspectStages`.
- `Organization\ProspectStatus` → `Organization\ProspectStage`; `Enums\Prospect\StatusTypes` → `Enums\Prospect\StageTypes` (cases unchanged).
- Includes: `organization_prospect_status` → `organization_prospect_stage`, `organization_prospect_status_path` → `organization_prospect_stage_path`.
- Filters: `organization_prospect_status_id` → `organization_prospect_stage_id`, `organization_prospect_status_path_id` → `organization_prospect_stage_path_id`.
- Fields: `status_type` → `stage_type`, and the `from_`/`to_`/`organization_project_`/`default_`/`default_inbound_` prospect-status IDs all become `..._stage_...`.
- Kanban column key `status` → `stage`; analytics `by_status` → `by_stage`, `by_status_type` → `by_stage_type`, `avg_`/`median_days_in_current_status` → `..._stage`.
- Portal: board filter `status_types` → `stage_types`; prospect writes take `organization_prospect_stage_id`.
- Prospect stages now require `organization_prospect_pipeline_id`. Existing stages were backfilled into a default pipeline.

### Added

- Payroll lane: `employments`, `workingTimeTerms`, `workingTimeDays`, and typed payslip `lines`. New docs: [Payroll](docs/organization/payroll.md).
- `payslips`: `organization_employment_id`, `period_start`, `period_end`, seven computed totals, `lines` and `organization_employment` includes, and `download()` on both lanes.
- `misc->determinePayslipLineCodes()` — which line codes apply to an employment and period.
- `prospectPipelines` service, with `board()` per pipeline.
- `prospects->duplicates()` and `prospects->merge()`, plus the `organization_prospect_pipeline_id` filter.
- `proposals->reopen()`, and the `proposal.reopened` trigger event.
- `invoiceNetworkExchanges->taxMapping()`, plus `tax_mapping` and `recording_suggestions` includes for recording an inbound bill. See [Integrations](docs/integrations.md).
- `users->activity()` accepts `limit` and `page`.
- `billingPackages`: `organization_bank_account_ids`.
- Export types `working_time_timesheet` and `payroll_handoff`, with `parameters.month`, `parameters.organization_employment_id` and `parameters.profile`.
- Portal `workingTimeDays->month()` / `attestMonth()`, and `payslips->download()`.
- 29 enums: `Employment\*` (8), `WorkingTime\*` (11), `Payslip\Line*` (3), `Prospect\Duplicate*` and `MergeBlockers`, `Tax\MappingSuggestion*`, `NetworkExchange\RecordingSuggestionBases`.
- Cases: `Product\Types::PENALTY`, `Tax\ProductTaxCategories::PENALTIES_COMPENSATION`, `EventTrail\EventType` gains `attested`, `jurisdiction_changed`, `agreement_changed`.

### Fixed

- `ProspectStage::$is_stuck_threshold_days` was annotated `bool`; it is a nullable integer.
- `prospects->duplicates()` hydrated rows as `Prospect` instead of `ProspectDuplicate`.

## [3.0.0] - 2026-08-30

The billing unit on every priced line became a UN/ECE code, and
`BankTransaction\States` lost four cases. **Your stored data was migrated for
you** — every product and line item already carries a `unit_code`, so nothing
needs backfilling. If you never wrote or read a unit, only product imports need a
look. See [UPGRADING](UPGRADING.md).

### Changed

- `unit_lang_map` → `unit_code` (a UN/ECE code such as `HUR`, `DAY`, `H87`) on
  products, invoice line items, proposal payment line items, billing-schedule
  phase line items, billing-package payment-plan phase line items and
  subscription-term items.
- `invoice_schema_map.peppol_billing_unit_code` removed, superseded by `unit_code`.
- `BankTransaction\States`: `classified`, `connected`, `connected_partially` and
  `danger` are now `completed` and `unbalanced`.
- `organizations->summary()`: `bank_transactions.unbalanced` replaces
  `partially_connected` and `danger`.
- Product imports: `field_position_unit_code` replaces
  `field_position_peppol_billing_unit_code`, `field_position_unit_map` is gone,
  and the unit column must hold a code — a label fails the row.
  See [Data Imports](docs/organization/data-imports.md).
- `aiAgents->run()` requires the `prompt_engine` feature, not `openai`.
- `match->run()` requires the entity's `*.manage` ability, not just membership.

### Added

- `due_at_from` / `due_at_to` filters on `invoices` and `receipts`.
- `description_lang_map` on bank-transaction cost types.
- `peppol_anaf_sync_status`, `peppol_anaf_sync_blocked_at` and
  `peppol_anaf_sync_blocked_reason` in `organizations->summary()`.
- `srt` and `vtt` file uploads.
- `match->run()`: `create_draft`, `bank_accounts`, and per-match provenance.

### Fixed

- Portal contacts without a platform account can save a billing profile and claim
  a package again; both answered `500`.
- `q` now folds case against lang-map and JSON columns.

## [2.8.1] - 2026-08-25

Reverts the one behaviour change in 2.8.0.

### Fixed

- **Organization user addresses are optional again**, whatever the role says.
  2.8.0 required `address_line_1` and `address_city` of users whose role can be
  invoiced, which broke integrations that create a customer before it has an
  address. The role branch is gone rather than loosened. If you added address
  fields to satisfy 2.8.0, you can drop them again.
- `address_line_1` now accepts an explicit `null`, not just an absent key, so
  clearing a stored street no longer depends on whether your client strips empty
  values before posting. This was true before 2.8.0 as well and is now fixed.

Docs only — no SDK method, property or enum changed in either direction.

## [2.8.0] - 2026-08-24

Proposals that quote in one currency and settle in another, a stage that reads
across a proposal's whole chain, per-party identity tiers on contracts, and
pipeline analytics for prospects.

Purely additive — no method, enum case, property or constant removed.

### Added

- **Proposal notification logs.** `$client->proposalNotificationLogs` —
  `list()` / `retrieve()` / `delete()` / `restore()` over every notice sent about
  a proposal; rows carry `is_seller_notification`. New docs:
  [Notification Logs](docs/organization/proposals.md#notification-logs).
- **Prospect pipeline analytics.** `analytics->prospects()` and
  `->prospectsByType($type)` for `summary`, `funnel`, `transitions`, `stalled`.
  New docs: [Pipeline Analytics](docs/organization/prospects.md#pipeline-analytics).
- **Settlement currency on proposals.** `allowed_currencies` and
  `exchange_rate_guarantee` writable; resource adds `billed_conversion`,
  `billed_currency_is_choosable`, `portal_url`. New docs:
  [Settlement Currency](docs/organization/proposals.md#settlement-currency).
- **Portal `proposals->refreshConversion($id)`** re-quotes a held rate;
  `proposals->accept()` accepts `billed_currency` and `displayed_amount`.
- **Proposal stage.** `stage` on the resource, plus a `stage_detail` include
  carrying `awaits`, `blockers` and `pending_signatures`. New docs:
  [Stages](docs/organization/proposals.md#stages).
- **Currency pair on billing packages.** `currency` and `currency_list` writable
  and read back; `exchange_rate_guarantee` sets when the rate fixes. New docs:
  [Currencies](docs/organization/billing-packages.md#currencies).
- **Contract party identity.** Parties accept `identity_requirement`,
  `party_citizenship`, `birthdate`, `birthplace`; billing-package contract
  template parties accept those plus `address_country_code`. New docs:
  [Party Identity](docs/organization/contracts.md#party-identity).
- **Organization users** accept and return `address_country_code`, `birthplace`
  and `citizenship` — portal profile lane too.
- **Prospect filters** `source_channel`, `source_medium`, `source_campaign`,
  `is_stalled`, and a `proposals` include. `board()` narrows by
  `assigned_organization_project_id` and a `created_at` window.
- **Prospect activity filters** `organization_prospect_status_path_id`,
  `activity_at_from/to`, `created_at_from/to`; resource adds
  `from_organization_prospect_status_id` and `to_organization_prospect_status_id`.
- **Five enums**: `Proposal\Stages`, `Proposal\StageActors`,
  `Proposal\NotificationLogTypes`, `Contract\PartyIdentityRequirements`,
  `BillingPackage\ExchangeRateGuarantees`.

### Changed

- `address_line_1` and `address_city` are now required when creating an
  organization user whose role can be invoiced, and cannot be blanked on update
  for such a user. Other roles are unaffected.
  **Reverted in 2.8.1 — do not build against this.**

### Fixed

- `docs/includes.md` listed the proposals row without `subscription_term`; the
  service has always accepted it.

## [2.7.0] - 2026-08-19

Public files that expire, quantity-tiered package pricing, contract templates
that name their own parties, an organization-facing trash, and a proposal that
can settle in a signature instead of an invoice.

One source break, scoped as a minor release by the maintainer: two
`BillingSchedule\Statuses` cases the API no longer sends have been removed
rather than left as phantoms. See [UPGRADING](UPGRADING.md).

### Added

- **Trash.** `$client->trashedItems` — `list()` reports what soft-deleted
  records are still held per entity (counts, reclaimable bytes, retention
  window, when the sweep is entitled to take them), and `purge()` permanently
  removes them ahead of that window. Both answer raw payloads rather than typed
  records.

  `purge(['entities' => [...]])` narrows to specific entity keys; omitting it
  empties everything self-service can reach. Records held for statutory reasons
  — invoices, proposals, receipts, payslips, contracts and signatures, billing
  schedules, network exchanges, bank accounts and transactions, users,
  organizations — are never reachable here whatever is passed, and appear in
  `list()` with `purgeable => false`. Both calls need `organization.manage`.

- **Billed identity for the Enlivy subscription.**
  `tenantBilling->billingIdentity()` and `->updateBillingIdentity()` read and
  override who the subscription is billed to, without changing the operating
  organization. `effective` always resolves whoever is actually billed; sending
  `custom_identity_name => null` clears the override. A stated name must arrive
  with its country code, address line, city, subdivision and zip code.

- **Retry a failed subscription charge.**
  `tenantBillingInvoices->charge($id)` returns the refreshed `Invoice`, with the
  attempt's outcome on the response meta as `charge_result`.

- **Event trails on organization users.** `organizationUsers->eventTrails()` and
  `->retrieveEventTrail()`, the same read-only audit surface invoices, receipts
  and billing schedules already expose.

- **Public files can carry a deadline.** `files->update()` accepts
  `public_access_expires_at`; once it passes, public access lapses without a
  further call. Must be in the future; `null` clears it. Reads back on the
  `File` resource.

- **Quantity price tiers on billing packages.** Group items and payment-plan
  phase line items accept `quantity_price_tiers`
  (`{price_type, tiers: [{min_quantity, price|discount_percent}]}`). Line items
  also accept `allow_quantity`, `min_quantity` and `max_quantity`. Chosen
  quantities travel back on `proposals->fromBillingPackage()` and on a portal
  claim — as `line_quantities` for a one-time package, and on
  `selected_group_items[].quantity` for a subscription. New enum
  `BillingPackage\TierPriceType` — `fixed`, `percent_of_baseline`.

- **Contract templates can name their own parties.** A template accepts
  `party_selection` and a `parties[]` cast. `parties` is a **default include**
  on a contract template, so it arrives with the template rather than needing to
  be requested — but it is deliberately absent from the customer-portal lane,
  where a template carries its `sections` only. New enums
  `BillingPackage\ContractPartySelections` (`standard`, `custom`) and
  `BillingPackage\ContractPartySources` (`sender`, `receiver`, `assigned`,
  `stated`).

- **Outcome mode.** Billing packages and proposals accept `outcome_mode`. Only
  `sale` produces revenue and therefore a fiscal document; `funding` (share
  subscriptions, loans, capital contributions, grants) and `agreement` (NDAs,
  framework agreements, term sheets) settle without one. New enum
  `BillingPackage\OutcomeMode`. `Proposal\Statuses` gains `agreed`, the
  terminal state such a proposal reaches from `accepted` once no required
  contract is outstanding.

- **`BillingSchedule\Statuses::PAYMENT_FAILED`.** A schedule whose card keeps
  refusing now stops minting cycles rather than accumulating one unpaid invoice
  a month, and comes back when money moves or a different card is attached.

- **When a schedule's invoice is issued.** Billing schedules accept
  `invoice_issue_trigger`. New enum `BillingSchedule\InvoiceIssueTrigger` —
  `on_generation` issues with the cycle, `on_payment` waits until the cycle is
  paid, so a failed collection does not burn a number in the gapless sequence.

- **An API token's scope is now editable.** `userTokens->update()` accepts
  `abilities` and `organizations`, so narrowing a token no longer means
  re-minting it. Both replace the existing set. `UserToken` gains
  `organizations`.

- **Lead attribution.** `Prospect` gains `source_medium`, `source_term`,
  `source_content` and `source_click_id`, writable on create and update — on the
  customer-portal lane as well as the back office.

- **Prospect activities can carry a file.** `organization_file_id` is writable,
  filterable, and available as the `organization_file` include.

- **Video and audio uploads.** `mp4`, `mov`, `webm`, `mp3` and `m4a` join the
  file allow-list, so a call recording can be stored and attached to a prospect
  activity.

- **Charge retry state on invoices.** An API-charged `Invoice` exposes
  `charge_first_failed_at`, `charge_retry_count`, `next_charge_retry_at` and
  `charge_retry_exhausted_at`. Present only on invoices Enlivy charges.

- **`Contract`** gains `content_locked_at`; **`ContractSignature`** gains
  `signed_document_hash`, a digest of the exact document that party signed.

- **New docs:** [Trash](docs/organization/trashed-items.md).

### Changed

- **`BillingSchedule\Statuses` no longer carries `subscription_required` or
  `cancelling`, and gains `payment_failed`.** See Removed below and
  [UPGRADING](UPGRADING.md).

- **An organization only speaks the languages it operates in.** A user's
  `locale`, and a billing package's `locale` and `locale_list`, must now be one
  of the organization's own locales — a 422 where the value used to be stored.
  Widen the organization's `locale_list` first. On update, a user already
  sitting on a locale the organization has since dropped keeps it.

- **Tax registration windows may not overlap.** Two registrations for the same
  `country_code` and `tax_family` covering the same day are now rejected with a
  422. Whether a seller was registered on a given date has to have one answer —
  it reaches `is_tax_charged` and the exemption code on issued documents. Close
  the current window with `effective_to` before opening the next. An edit that
  leaves the window and its scope alone is never blocked, so an already-tangled
  timeline can still be corrected.

- **`source_channel` is capped at 100 characters on the customer-portal lane**,
  matching the back office, which already enforced it. A portal-created prospect
  with a longer value now gets a 422 instead of storing it.

- **A signed contract's content is frozen.** `content_locked_at` is stamped when
  the contract is signed; edits to the content are rejected from then on.

- **`outcome_mode` is write-once on proposals** — accepted on `create()`,
  rejected on `update()`.

### Removed

- **`BillingSchedule\Statuses::SUBSCRIPTION_REQUIRED` and
  `::CANCELLING`.** The API no longer sends either value, and neither ever
  reached a production row. `cancelling` was a second spelling of
  `cancel_effective_at` — a schedule the customer has asked to end stays
  `active` until it reaches `cancelled`. `subscription_required` stamped the
  organization's entitlement onto its schedule rows and has no replacement; the
  payments cron reads that entitlement directly and writes nothing. Referencing
  either constant is a fatal — see [UPGRADING](UPGRADING.md).

### Fixed

- **`DELETE` request parameters were silently dropped.** The cURL transport put
  params on the query string for `GET` and in the body for
  `POST`/`PUT`/`PATCH`, but did neither for `DELETE`, so anything passed to a
  delete call never reached the wire. This matters for
  `trashedItems->purge(['entities' => ...])`, where a vanished filter would
  have widened an irreversible purge to everything. `DELETE` params now travel
  on the query string, as the API reads them.

- **The documented file-extension list was wrong.** `ppt`, `pptx`, `rar` and
  `webp` were never accepted by the API. The list now matches the actual
  allow-list, and notes the post-upload `Content-Type` check.

- **`docs/filters.md` had no `prospectActivities` section** despite the service
  declaring filters. Added, including the new `organization_file_id`.

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

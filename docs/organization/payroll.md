# Payroll: Employment, Working Time, Payslips

Three resources make up the payroll lane:

- **Employment** — the engagement itself: who, under what contract, in which jurisdiction, on what pay period.
- **Working time** — **terms** (the contracted norm) and **days** (what was actually worked or missed).
- **Payslips** — now carrying typed **lines**, so a slip states its own arithmetic instead of a single total.

An employment is the anchor: working-time terms, working-time days and payslips all hang off `organization_employment_id`.

## Employment

```php
use Enlivy\Enums\Employment\PayPeriods;
use Enlivy\Enums\Employment\Types;

$employment = $client->employments->create([
    'organization_user_id' => 'org_user_xxx',
    'organization_contract_id' => 'org_con_xxx',
    'type' => Types::PERMANENT->value,
    'jurisdiction_code' => 'RO',
    'timezone' => 'Europe/Bucharest',
    'workweek_start' => 'monday',
    'job_title' => 'Backend Engineer',
    'start_date' => '2026-09-01',
    'pay_period' => PayPeriods::MONTHLY->value,
]);
```

| Field | Notes |
|-------|-------|
| `organization_user_id` | The person employed |
| `organization_contract_id` | Optional link to the signed contract |
| `type` | `Employment\Types` |
| `jurisdiction_code`, `jurisdiction_subdivision_iso_3166` | Where the work is governed |
| `timezone`, `workweek_start` | Drive day boundaries and weekend detection |
| `contract_number`, `contract_date`, `occupation_code`, `job_title`, `work_site` | Descriptive |
| `start_date`, `end_date`, `end_reason` | The engagement window |
| `start_date_source`, `end_date_source` | `Employment\FactSources` — how the date is known |
| `pay_period` | `Employment\PayPeriods` |
| `registry_status`, `registry_reference`, `registry_last_verified_at` | `Employment\RegistryStatuses` and the authority reference |

`lifecycle` is **derived, not stored**: the API computes `scheduled` / `active` / `ended` / `undated`
from the dates on each read. Do not send it.

```php
$active = $client->employments->list([
    'type' => Types::PERMANENT->value,
    'active_on' => '2026-09-01',
    'include' => 'organization_user,organization_employment_jurisdictions',
]);
```

Jurisdictions and agreements are read-only sub-records reached through includes:
`organization_employment_jurisdictions` splits the governing country per `Employment\JurisdictionAxes`
(work, payroll, social security, tax) when they differ, and `organization_employment_agreements`
records opt-outs such as a weekly-hours waiver.

Employments soft-delete and restore like other org resources:

```php
$client->employments->delete($employment->id);
$client->employments->restore($employment->id);
```

## Working-time terms

A term is the contracted norm for a window. Terms do not overlap; close one by setting
`effective_to` before opening the next.

```php
use Enlivy\Enums\WorkingTime\TermUnits;

$term = $client->workingTimeTerms->create([
    'organization_employment_id' => $employment->id,
    'effective_from' => '2026-09-01',
    'norm_hours_per_day' => 8,
    'norm_days_per_week' => 5,
    'unit' => TermUnits::HOURS->value,
    'schedule_pattern' => ['mon' => 8, 'tue' => 8, 'wed' => 8, 'thu' => 8, 'fri' => 8],
]);

// The term in force on a given day
$inForce = $client->workingTimeTerms->list([
    'organization_employment_id' => $employment->id,
    'effective_on' => '2026-09-15',
]);
```

## Working-time days

Days are read as a list or edited a **month at a time**. There is no per-day create or delete —
the month grid is the unit of work.

```php
$days = $client->workingTimeDays->list([
    'organization_employment_id' => $employment->id,
    'date_from' => '2026-09-01',
    'date_to' => '2026-09-30',
    'include' => 'organization_working_time_day_breaks',
]);

$month = $client->workingTimeDays->month([
    'organization_employment_id' => $employment->id,
    'month' => '2026-09',
]);
```

Saving a month replaces the grid for the days you send:

```php
use Enlivy\Enums\WorkingTime\BreakTypes;
use Enlivy\Enums\WorkingTime\DayDispositions;

$client->workingTimeDays->upsertMonth([
    'organization_employment_id' => $employment->id,
    'month' => '2026-09',
    'days' => [
        [
            'date' => '2026-09-01',
            'disposition' => DayDispositions::WORKED->value,
            'started_at' => '2026-09-01T09:00:00Z',
            'ended_at' => '2026-09-01T18:00:00Z',
            'break_minutes' => 60,
            'expected_version' => 3,
            'breaks' => [
                [
                    'type' => BreakTypes::MEAL->value,
                    'started_at' => '2026-09-01T13:00:00Z',
                    'ended_at' => '2026-09-01T14:00:00Z',
                    'is_paid' => false,
                ],
            ],
        ],
        [
            'date' => '2026-09-02',
            'disposition' => DayDispositions::ABSENT->value,
            'absence_reason' => 'annual_leave',
        ],
    ],
]);
```

Each day carries a `version`. Send `expected_version` to make the write conditional on nobody
having changed that day since you read it — a mismatch is rejected rather than silently overwriting.

`worked_hours`, `night_hours`, `overtime_hours` and `weekend_hours` are derived from the times,
breaks and the term in force; `derived_at` says when. Send times, not totals, unless you are
importing a system that only has totals.

### Attesting a month

Attestation is a two-party confirmation, and the two lanes are deliberately different:

```php
use Enlivy\Enums\WorkingTime\AttestationMethods;

// Back office — a second party confirms.
$client->workingTimeDays->attestMonth([
    'organization_employment_id' => $employment->id,
    'month' => '2026-09',
    'attestation_method' => AttestationMethods::SUPERVISOR_APPROVED->value,
]);

// Customer portal — the worker confirms their own month.
$portal->workingTimeDays->attestMonth([
    'organization_employment_id' => $employment->id,
    'month' => '2026-09',
]);
```

The back-office endpoint rejects `worker_confirmed`: only `supervisor_approved`, `period_close`
and `signed_document` are accepted there. The portal endpoint takes no method at all — it always
records `worker_confirmed`, because the caller *is* the worker. `attestation_status` and
`last_attested_version` then tell you whether a later edit invalidated the confirmation.

`absence_reason` is withheld from viewers who may not see it. Check `absence_reason_redacted`
before treating a null reason as "no reason given".

## Payslip lines

A payslip now carries typed lines and a total per line type.

```php
use Enlivy\Enums\Payslip\LineCodes;
use Enlivy\Enums\Payslip\LineTypes;

$payslip = $client->payslips->create([
    'organization_employment_id' => $employment->id,
    'period_start' => '2026-09-01',
    'period_end' => '2026-09-30',
    'currency' => 'RON',
    'lines' => [
        [
            'type' => LineTypes::EARNING->value,
            'code' => LineCodes::BASE_SALARY->value,
            'amount' => 12000,
            'order' => 1,
        ],
        [
            'type' => LineTypes::EMPLOYEE_CONTRIBUTION->value,
            'code' => LineCodes::PENSION_EMPLOYEE->value,
            'amount' => 3000,
            'order' => 2,
        ],
    ],
]);

$payslip = $client->payslips->retrieve($payslip->id, [
    'include' => 'lines,organization_employment',
]);

foreach ($payslip->lines as $line) {
    echo "{$line->code}: {$line->amount} {$line->currency}\n";
}
```

The totals are computed from the lines, not sent: `gross_total`, `employee_contributions_total`,
`tax_relief_total`, `taxable_amount`, `post_tax_deductions_total`, `paid_total` and
`employer_contributions_total` sit alongside the existing `net_total`, `tax_total` and `total`.

Which codes apply depends on the employment and the period, so ask rather than hard-code:

```php
$codes = $client->misc->determinePayslipLineCodes([
    'organization_employment_id' => $employment->id,
    'period_end' => '2026-09-30',
]);
```

A slip can be downloaded as a document on both lanes:

```php
$pdf = $client->payslips->download($payslip->id);
$pdf = $portal->payslips->download($payslip->id);
```

## Exports

Two export types cover the lane. Both take `parameters.month`, and optionally
`parameters.organization_employment_id` to narrow to one person:

```php
use Enlivy\Enums\ExportData\Types;

$client->exportData->create([
    'type' => Types::WORKING_TIME_TIMESHEET->value,
    'parameters' => ['month' => '2026-09'],
]);

$client->exportData->create([
    'type' => Types::PAYROLL_HANDOFF->value,
    'parameters' => ['month' => '2026-09', 'profile' => 'default'],
]);
```

`PAYROLL_HANDOFF` also accepts `parameters.profile`, which selects the shape the receiving
payroll system expects.

## Related

- [Organization Users](users.md) — the people employments point at
- [Contracts](contracts.md) — the signed agreement an employment references
- [Customer Portal](customer-portal.md) — the worker-facing month and payslip download
- [Enums](../enums.md) — `Employment\*`, `WorkingTime\*`, `Payslip\Line*`

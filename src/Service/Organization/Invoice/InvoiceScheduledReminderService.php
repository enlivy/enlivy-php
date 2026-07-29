<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for reading the payment reminders an organization is about to send.
 *
 * Rows are projected from the current reminder settings on every read, not stored, so they carry
 * no `id` and change the moment those settings change — cache them no longer than one request.
 */
class InvoiceScheduledReminderService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'invoices/scheduled-reminders';

    public const array AVAILABLE_INCLUDES = [];

    public const array AVAILABLE_FILTERS = [
        'from',
        'to',
        'type',
        'organization_invoice_id',
    ];

    /**
     * List the reminders scheduled inside a window.
     *
     * Filters:
     * - `from` (datetime) - Window start; defaults to now
     * - `to` (datetime) - Window end; defaults to 30 days out, capped at 366 days from now
     * - `type` (string: email_reminder_upcoming|email_reminder_overdue) - Only this reminder type
     * - `organization_invoice_id` (string) - Only reminders for this invoice
     *
     * Each row carries `organization_invoice_id`, `organization_invoice_number`, `type`,
     * `scheduled_for`, `sequence`, `due_at`, `total`, `currency` and `recipient_email`.
     * The response `meta` echoes the window actually walked (`from`, `to`, `count`).
     *
     * @return Collection<EnlivyObject>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<EnlivyObject> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}

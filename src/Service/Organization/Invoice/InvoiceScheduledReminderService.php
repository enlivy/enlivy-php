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
     * `from` defaults to now and `to` to 30 days out, capped at 366. The window
     * is clamped server-side, so read `meta.to` back rather than assuming the
     * one you asked for was honoured.
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

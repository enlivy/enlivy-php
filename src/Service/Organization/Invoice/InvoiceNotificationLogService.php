<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\Organization\InvoiceNotificationLog;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * @method InvoiceNotificationLog restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class InvoiceNotificationLogService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'invoices/notification-logs';
    protected const ?string RESOURCE_CLASS = InvoiceNotificationLog::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_invoice_id',
        'types',
        'created_at_from',
        'created_at_to',
    ];

    /**
     * @return Collection<InvoiceNotificationLog>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<InvoiceNotificationLog> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var InvoiceNotificationLog */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var InvoiceNotificationLog */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

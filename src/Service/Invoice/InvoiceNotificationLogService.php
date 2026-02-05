<?php

declare(strict_types=1);

namespace Enlivy\Service\Invoice;

use Enlivy\Collection;
use Enlivy\InvoiceNotificationLog;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing invoice notification logs.
 *
 * @method InvoiceNotificationLog restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class InvoiceNotificationLogService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'invoice-notification-logs';

    /**
     * @return Collection<InvoiceNotificationLog>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNotificationLog
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceNotificationLog */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): InvoiceNotificationLog
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoiceNotificationLog */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\Invoice;
use Enlivy\Util\RequestOptions;

class InvoiceService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = Invoice::class;

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'invoices'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Invoice
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->portalPath($orgId, "invoices/{$id}"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "invoices/{$id}/download"), $params, $opts);
    }
}

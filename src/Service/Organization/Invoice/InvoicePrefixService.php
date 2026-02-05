<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Invoice;

use Enlivy\Collection;
use Enlivy\Organization\InvoicePrefix;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing invoice prefixes.
 *
 * @method InvoicePrefix restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class InvoicePrefixService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'invoice-prefixes';

    /**
     * @return Collection<InvoicePrefix>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): InvoicePrefix
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoicePrefix */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): InvoicePrefix
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoicePrefix */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): InvoicePrefix
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoicePrefix */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): InvoicePrefix
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var InvoicePrefix */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

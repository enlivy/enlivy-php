<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Receipt;

use Enlivy\Collection;
use Enlivy\Organization\Receipt;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing receipts.
 *
 * @method Receipt restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ReceiptService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;

    protected const string RESOURCE = 'receipts';

    /**
     * @return Collection<Receipt>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Receipt */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Receipt
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Receipt */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Receipt
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Receipt */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Receipt
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Receipt */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

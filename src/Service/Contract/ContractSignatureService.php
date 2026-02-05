<?php

declare(strict_types=1);

namespace Enlivy\Service\Contract;

use Enlivy\Collection;
use Enlivy\ContractSignature;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing contract signatures.
 *
 * @method ContractSignature restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ContractSignatureService extends AbstractService
{
    use HasRestore;

    protected const string RESOURCE = 'contract-signatures';

    /**
     * @return Collection<ContractSignature>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ContractSignature
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ContractSignature */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ContractSignature
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ContractSignature */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ContractSignature
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ContractSignature */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ContractSignature
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ContractSignature */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function downloadEvidence(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-evidence"), $params, $opts);
    }
}

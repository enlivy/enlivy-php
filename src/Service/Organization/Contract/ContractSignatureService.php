<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Contract;

use Enlivy\Collection;
use Enlivy\Organization\ContractSignature;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing contract signatures.
 *
 * @method ContractSignature restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ContractSignatureService extends AbstractService
{
    use HasRestore;
    use HasIncludes;

    protected const string RESOURCE = 'contract-signatures';
    protected const ?string RESOURCE_CLASS = ContractSignature::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'organization_contract',
        'evidence_authentication',
        'evidence_consent',
        'evidence_signature_biometrics',
        'sign_session_url',
    ];

    /**
     * @return Collection<ContractSignature>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ContractSignature
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): ContractSignature
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): ContractSignature
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ContractSignature
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function downloadEvidence(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download-evidence"), $params, $opts);
    }
}

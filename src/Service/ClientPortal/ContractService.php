<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\Contract;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ContractService extends AbstractPortalService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = Contract::class;

    public const array AVAILABLE_INCLUDES = [
        'sender_user',
        'receiver_user',
        'file',
        'contract_status',
        'contract_chapters',
        'contract_parties',
        'contract_prefix',
        'abilities',
    ];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->portalPath($orgId, 'contracts'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Contract
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Contract */
        return $this->request('GET', $this->portalPath($orgId, "contracts/{$id}"), $params, $opts);
    }

    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "contracts/{$id}/download"), $params, $opts);
    }
}

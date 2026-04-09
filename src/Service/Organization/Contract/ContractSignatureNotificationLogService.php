<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Contract;

use Enlivy\Collection;
use Enlivy\Organization\ContractSignatureNotificationLog;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for viewing contract signature notification logs.
 */
class ContractSignatureNotificationLogService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'contract-signature-notification-logs';
    protected const ?string RESOURCE_CLASS = ContractSignatureNotificationLog::class;

    public const array AVAILABLE_INCLUDES = [
        'organization_contract',
        'organization_contract_signature',
        'organization',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_contract_id',
        'organization_contract_signature_id',
    ];

    /**
     * @return Collection<ContractSignatureNotificationLog>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ContractSignatureNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

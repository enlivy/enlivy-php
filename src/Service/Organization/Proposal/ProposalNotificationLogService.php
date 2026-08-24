<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Proposal;

use Enlivy\Collection;
use Enlivy\Organization\ProposalNotificationLog;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * @method ProposalNotificationLog restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class ProposalNotificationLogService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'proposals/notification-logs';
    protected const ?string RESOURCE_CLASS = ProposalNotificationLog::class;

    public const array AVAILABLE_INCLUDES = [
        'deleted_by_user',
        'organization',
        'proposal',
    ];

    public const array AVAILABLE_FILTERS = [
        'organization_proposal_id',
        'types',
        'created_at_from',
        'created_at_to',
    ];

    /**
     * @return Collection<ProposalNotificationLog>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ProposalNotificationLog> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): ProposalNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ProposalNotificationLog */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): ProposalNotificationLog
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var ProposalNotificationLog */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

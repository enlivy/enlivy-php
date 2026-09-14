<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\HelpdeskProactiveMessage;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * @method HelpdeskProactiveMessage restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class HelpdeskProactiveMessageService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;
    protected const string RESOURCE = 'helpdesk/proactive-messages';
    protected const ?string RESOURCE_CLASS = HelpdeskProactiveMessage::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'inbox',
        'sender_teammate',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'is_active',
        'organization_helpdesk_inbox_id',
    ];

    /**
     * @return Collection<HelpdeskProactiveMessage>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskProactiveMessage> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskProactiveMessage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskProactiveMessage */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): HelpdeskProactiveMessage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskProactiveMessage */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): HelpdeskProactiveMessage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskProactiveMessage */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * The desk answers a delete with a status envelope rather than the deleted row.
     */
    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }
}

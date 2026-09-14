<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\Organization\HelpdeskVisitor;
use Enlivy\Organization\HelpdeskVisitorEvent;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * A returning browser on a site running the widget, and what it did there.
 *
 * One row per organization, not per inbox. Visitors are never created or edited through the API;
 * the widget mints them. Blocking is how an abuser is stopped.
 */
class HelpdeskVisitorService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'helpdesk/visitors';
    protected const ?string RESOURCE_CLASS = HelpdeskVisitor::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'organization_user',
        'organization_prospect',
        'events',
    ];

    public const array AVAILABLE_FILTERS = [
        'identified',
        'blocked',
    ];

    public const array EVENT_INCLUDES = [
        'organization',
        'visitor',
        'proactive_message',
        'conversation',
    ];

    /**
     * @return Collection<HelpdeskVisitor>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskVisitor> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskVisitor
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskVisitor */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * @return Collection<HelpdeskVisitorEvent>
     */
    public function events(string $visitorId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskVisitorEvent> */
        return $this->requestCollection(
            'GET',
            $this->orgPath($orgId, self::RESOURCE . "/{$visitorId}/events"),
            $params,
            $opts,
            HelpdeskVisitorEvent::class,
        );
    }

    public function block(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskVisitor
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskVisitor */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/block"), $params, $opts);
    }

    public function unblock(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskVisitor
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskVisitor */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/unblock"), $params, $opts);
    }
}

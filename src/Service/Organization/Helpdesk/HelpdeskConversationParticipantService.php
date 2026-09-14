<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\HelpdeskConversationParticipant;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Everyone copied on a thread besides the contact.
 *
 * An address cannot be changed after the fact: remove the participant and add the right one.
 */
class HelpdeskConversationParticipantService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const ?string RESOURCE_CLASS = HelpdeskConversationParticipant::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'conversation',
        'organization_user',
        'organization_prospect',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<HelpdeskConversationParticipant>
     */
    public function list(string $conversationId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskConversationParticipant> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/participants"), $params, $opts);
    }

    public function create(string $conversationId, array $params, ?RequestOptions $opts = null): HelpdeskConversationParticipant
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationParticipant */
        return $this->request('POST', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/participants"), $params, $opts);
    }

    public function update(string $conversationId, string $participantId, array $params, ?RequestOptions $opts = null): HelpdeskConversationParticipant
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationParticipant */
        return $this->request('PUT', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/participants/{$participantId}"), $params, $opts);
    }

    public function delete(string $conversationId, string $participantId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/participants/{$participantId}"), $params, $opts);
    }
}

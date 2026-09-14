<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Collection;
use Enlivy\Organization\HelpdeskConversation;
use Enlivy\Organization\HelpdeskConversationMessage;
use Enlivy\Util\RequestOptions;

/**
 * The customer's own side of the desk.
 *
 * This lane shows a customer only their own threads, and only the part of each thread meant for
 * them: internal notes and activity rows never appear, and a thread marked spam is never reported
 * as such. There is no paging and no ordering to choose — newest activity leads.
 *
 * The inbox is not the caller's to pick; the organization's default customer-facing one is used.
 */
class HelpdeskConversationService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = HelpdeskConversation::class;

    public const array AVAILABLE_INCLUDES = [
        'messages',
    ];

    /**
     * @return Collection<HelpdeskConversation>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskConversation> */
        return $this->requestCollection('GET', $this->portalPath($orgId, 'helpdesk/conversations'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('GET', $this->portalPath($orgId, "helpdesk/conversations/{$id}"), $params, $opts);
    }

    /**
     * Takes `subject`, `locale`, `content`, `content_type` and `attachment_ids`. Who is asking
     * comes from the portal session, so no contact details are accepted here.
     */
    public function create(array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('POST', $this->portalPath($orgId, 'helpdesk/conversations'), $params, $opts);
    }

    /**
     * @return Collection<HelpdeskConversationMessage>
     */
    public function messages(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskConversationMessage> */
        return $this->requestCollection(
            'GET',
            $this->portalPath($orgId, "helpdesk/conversations/{$id}/messages"),
            $params,
            $opts,
            HelpdeskConversationMessage::class,
        );
    }

    public function postMessage(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversationMessage
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationMessage */
        return $this->request(
            'POST',
            $this->portalPath($orgId, "helpdesk/conversations/{$id}/messages"),
            $params,
            $opts,
            HelpdeskConversationMessage::class,
        );
    }

    public function markRead(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('POST', $this->portalPath($orgId, "helpdesk/conversations/{$id}/read"), $params, $opts);
    }

    /**
     * `rating` is 1 to 5, with an optional `rating_comment`.
     */
    public function rate(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('POST', $this->portalPath($orgId, "helpdesk/conversations/{$id}/rating"), $params, $opts);
    }

    /**
     * Answers 204, so there is nothing to read back.
     */
    public function typing(string $id, array $params, ?RequestOptions $opts = null): void
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        $this->requestRaw('POST', $this->portalPath($orgId, "helpdesk/conversations/{$id}/typing"), $params, $opts);
    }
}

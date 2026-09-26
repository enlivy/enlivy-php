<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\HelpdeskConversation;
use Enlivy\Organization\HelpdeskConversationRead;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Util\RequestOptions;

/**
 * A thread on the desk, whichever channel it arrived on.
 *
 * State is derived rather than stored, so there is no status field to write: use the verbs.
 *
 * @method HelpdeskConversation restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class HelpdeskConversationService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'helpdesk/conversations';
    protected const ?string RESOURCE_CLASS = HelpdeskConversation::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'inbox',
        'organization_project',
        'visitor',
        'assigned_teammate',
        'contact_organization_user',
        'contact_organization_prospect',
        'messages',
        'attachments',
        'participants',
        'reads',
        'merged_into',
        'continued_from',
        'tag_ids',
        'deleted_by_user',
        'lifecycle',
    ];

    public const array AVAILABLE_FILTERS = [
        'state',
        'organization_helpdesk_inbox_id',
        'assigned_organization_helpdesk_teammate_id',
        'assignment',
        'has_unread',
        'priority',
        'source',
        'contact_email',
        'contact_organization_user_id',
        'contact_organization_prospect_id',
        'organization_project_id',
    ];

    /**
     * @return Collection<HelpdeskConversation>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskConversation> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
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

    public function assign(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('assign', $id, $params, $opts);
    }

    public function unassign(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('unassign', $id, $params, $opts);
    }

    public function snooze(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('snooze', $id, $params, $opts);
    }

    public function resolve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('resolve', $id, $params, $opts);
    }

    public function close(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('close', $id, $params, $opts);
    }

    public function reopen(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('reopen', $id, $params, $opts);
    }

    /**
     * Withholds the attachments and tells the sender nothing.
     */
    public function markSpam(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('spam', $id, $params, $opts);
    }

    public function unmarkSpam(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('not-spam', $id, $params, $opts);
    }

    /**
     * Blocks the contact's address, or their whole domain with `whole_domain`, and marks the
     * thread as spam.
     */
    public function blockSender(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('block-sender', $id, $params, $opts);
    }

    public function promoteProspect(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('prospect', $id, $params, $opts);
    }

    /**
     * Fold this thread into another. The merged one stays as a closed stub, so old links resolve.
     */
    public function merge(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('merge', $id, $params, $opts);
    }

    /**
     * Answers with the read row, not the conversation.
     */
    public function markRead(string $id, array $params, ?RequestOptions $opts = null): HelpdeskConversationRead
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationRead */
        return $this->request(
            'POST',
            $this->orgPath($orgId, self::RESOURCE . "/{$id}/read"),
            $params,
            $opts,
            HelpdeskConversationRead::class,
        );
    }

    /**
     * Clears only the caller's own read watermark, so the whole thread reads unread for them alone.
     */
    public function markUnread(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskConversation
    {
        return $this->action('unread', $id, $params, $opts);
    }

    /**
     * Answers 204, so there is nothing to read back.
     */
    public function typing(string $id, array $params, ?RequestOptions $opts = null): void
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        $this->requestRaw('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/typing"), $params, $opts);
    }

    /**
     * Who is currently on this thread, each side with when it was last seen.
     */
    public function presence(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/presence"), $params, $opts);
    }

    private function action(string $verb, string $id, array $params, ?RequestOptions $opts): HelpdeskConversation
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversation */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/{$verb}"), $params, $opts);
    }
}

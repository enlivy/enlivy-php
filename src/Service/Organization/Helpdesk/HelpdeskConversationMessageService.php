<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\Organization\HelpdeskConversationMessage;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Messages hang off a conversation, so every call names its thread first.
 *
 * A message cannot be edited or deleted; `redact()` is how a line is taken back.
 */
class HelpdeskConversationMessageService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const ?string RESOURCE_CLASS = HelpdeskConversationMessage::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'conversation',
        'author_organization_user',
        'author_organization_prospect',
        'attachments',
        'deliveries',
    ];

    public const array AVAILABLE_FILTERS = [
        'type',
    ];

    /**
     * @return Collection<HelpdeskConversationMessage>
     */
    public function list(string $conversationId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskConversationMessage> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/messages"), $params, $opts);
    }

    /**
     * `type` accepts `outgoing` or `note` only — an incoming message is the customer's to write.
     * Pass `deliver_email` false to record a reply without mailing it.
     */
    public function create(string $conversationId, array $params, ?RequestOptions $opts = null): HelpdeskConversationMessage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationMessage */
        return $this->request('POST', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/messages"), $params, $opts);
    }

    public function redact(string $conversationId, string $messageId, array $params = [], ?RequestOptions $opts = null): HelpdeskConversationMessage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationMessage */
        return $this->request('POST', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/messages/{$messageId}/redact"), $params, $opts);
    }
}

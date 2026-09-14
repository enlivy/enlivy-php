<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\EnlivyObject;
use Enlivy\Organization\HelpdeskConversationAttachment;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Files on a thread. Uploads are multipart, with the file under `file`.
 *
 * A file can be staged before the conversation exists — `stage()` returns a row whose id is then
 * passed as one of `attachment_ids` when the conversation or message is created.
 */
class HelpdeskConversationAttachmentService extends AbstractService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = HelpdeskConversationAttachment::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'conversation',
        'message',
        'visitor',
        'uploaded_by_user',
    ];

    /**
     * Staged rows are swept if nothing claims them, so submit within the day.
     */
    public function stage(array $params, ?RequestOptions $opts = null): HelpdeskConversationAttachment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationAttachment */
        return $this->request('POST', $this->orgPath($orgId, 'helpdesk/attachments'), $params, $opts);
    }

    public function create(string $conversationId, array $params, ?RequestOptions $opts = null): HelpdeskConversationAttachment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationAttachment */
        return $this->request('POST', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/attachments"), $params, $opts);
    }

    public function download(string $conversationId, string $attachmentId, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/attachments/{$attachmentId}/download"), $params, $opts);
    }

    public function delete(string $conversationId, string $attachmentId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "helpdesk/conversations/{$conversationId}/attachments/{$attachmentId}"), $params, $opts);
    }
}

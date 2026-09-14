<?php

declare(strict_types=1);

namespace Enlivy\Service\ClientPortal;

use Enlivy\Organization\HelpdeskConversationAttachment;
use Enlivy\Util\RequestOptions;

/**
 * Files the customer attaches to their own thread. Uploads are multipart, under `file`.
 */
class HelpdeskAttachmentService extends AbstractPortalService
{
    protected const ?string RESOURCE_CLASS = HelpdeskConversationAttachment::class;

    /**
     * Upload a file, then pass the returned id as one of `attachment_ids` when posting.
     */
    public function create(array $params, ?RequestOptions $opts = null): HelpdeskConversationAttachment
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskConversationAttachment */
        return $this->request('POST', $this->portalPath($orgId, 'helpdesk/attachments'), $params, $opts);
    }

    public function download(string $attachmentId, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->portalPath($orgId, "helpdesk/attachments/{$attachmentId}/download"), $params, $opts);
    }
}

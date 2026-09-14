<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Collection;
use Enlivy\Organization\HelpdeskInboundEmail;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * The raw mail the desk took in, and what it decided to do with each piece.
 *
 * Read-only apart from `reprocess()`, which runs a message back through the pipeline after a
 * routing rule or an inbox address has changed.
 */
class HelpdeskInboundEmailService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'helpdesk/inbound-emails';
    protected const ?string RESOURCE_CLASS = HelpdeskInboundEmail::class;

    /**
     * `content` is the raw body, withheld unless asked for by name.
     */
    public const array AVAILABLE_INCLUDES = [
        'organization',
        'inbox',
        'organization_api_credential',
        'conversation',
        'message',
        'content',
    ];

    public const array AVAILABLE_FILTERS = [
        'interpretation',
        'organization_helpdesk_inbox_id',
        'organization_helpdesk_conversation_id',
        'processed',
    ];

    /**
     * @return Collection<HelpdeskInboundEmail>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<HelpdeskInboundEmail> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskInboundEmail */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function reprocess(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskInboundEmail */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/reprocess"), $params, $opts);
    }
}

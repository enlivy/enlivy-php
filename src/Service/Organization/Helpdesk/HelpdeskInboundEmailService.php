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
 */
class HelpdeskInboundEmailService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'helpdesk/inbound-emails';
    protected const ?string RESOURCE_CLASS = HelpdeskInboundEmail::class;

    /**
     * `content` (with `content_type`), `headers` and `trust_assessment` read null unless asked for
     * by name.
     */
    public const array AVAILABLE_INCLUDES = [
        'organization',
        'inbox',
        'organization_api_credential',
        'blocked_identifier',
        'conversation',
        'message',
        'content',
        'headers',
        'trust_assessment',
    ];

    public const array AVAILABLE_FILTERS = [
        'interpretation',
        'category',
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

    /**
     * Runs a message back through the pipeline after a routing rule or an inbox address changed.
     */
    public function reprocess(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        return $this->action('reprocess', $id, $params, $opts);
    }

    /**
     * Releases `quarantined`, `bulk`, `auto_reply` or unclassified mail while its body is stored.
     * `trust_sender` (default true) and `trust_domain` also trust the sender or domain for next time.
     */
    public function promote(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        return $this->action('promote', $id, $params, $opts);
    }

    /**
     * `interpretation` is `bulk`, `discarded` or `spam`. `apply_to_sender` and `apply_to_domain`
     * also write a discard rule for the sender's future mail.
     */
    public function classify(string $id, array $params, ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        return $this->action('classify', $id, $params, $opts);
    }

    public function blockSender(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        return $this->action('block-sender', $id, $params, $opts);
    }

    /**
     * Reads the body back from the mailbox it arrived in, for mail whose stored body has aged
     * out. It arrives in `content` and is not stored again.
     */
    public function fetchOriginal(string $id, array $params = [], ?RequestOptions $opts = null): HelpdeskInboundEmail
    {
        return $this->action('fetch-original', $id, $params, $opts);
    }

    private function action(string $verb, string $id, array $params, ?RequestOptions $opts): HelpdeskInboundEmail
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskInboundEmail */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/{$verb}"), $params, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ServiceIntegration;

use Enlivy\EnlivyObject;
use Enlivy\Collection;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Util\RequestOptions;

class ServiceIntegrationService extends AbstractService
{
    use HasFilters;

    public const array AVAILABLE_FILTERS = [];

    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateFilters($params);
        return $this->requestCollection('GET', '/service-integration', $params, $opts);
    }

    /**
     * The Slack channels the connected workspace exposes, to pick a delivery target.
     */
    public function slackChannels(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, 'service-integration/slack/channels'), $params, $opts);
    }

    /**
     * Post a test message to the connected Slack workspace.
     */
    public function slackTest(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, 'service-integration/slack/test'), $params, $opts);
    }

    /**
     * Begin the Gmail connection flow. Returns the form the browser then submits.
     */
    public function gmailConnect(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, 'service-integration/gmail/connect'), $params, $opts);
    }
}

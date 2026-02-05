<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Webhook;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing webhooks.
 */
class WebhookService extends AbstractService
{
    protected const string RESOURCE = 'webhooks';

    /**
     * @return Collection<Webhook>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Webhook
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Webhook */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Webhook
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Webhook */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Webhook
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Webhook */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Webhook
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Webhook */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * @return Collection<EnlivyObject>
     */
    public function events(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . '/events'), $params, $opts);
    }

    /**
     * @return Collection<EnlivyObject>
     */
    public function notifications(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . '/notifications'), $params, $opts);
    }

    public function retrieveNotification(string $id, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/notifications/{$id}"), $params, $opts);
    }
}

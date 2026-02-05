<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\AiAgent;
use Enlivy\Util\RequestOptions;

/**
 * AI Agent service (read-only for non-admin users).
 *
 * Note: create/update/delete operations require administrator privileges.
 */
class AiAgentService extends AbstractService
{
    /**
     * @return Collection<AiAgent>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', '/ai-agents', $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): AiAgent
    {
        /** @var AiAgent */
        return $this->request('GET', "/ai-agents/{$id}", $params, $opts);
    }

    /**
     * Run an AI agent with the given input.
     */
    public function run(string $id, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/ai-agents/{$id}/run", $params, $opts);
    }
}

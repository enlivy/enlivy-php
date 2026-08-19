<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for the organization's trash — what soft-deleted records are still held, and
 * permanently removing them ahead of the retention window.
 *
 * Not a record resource: both calls answer with a raw report rather than a typed row.
 */
class TrashedItemsService extends AbstractService
{
    protected const string RESOURCE = 'trashed-items';

    /**
     * What is in the trash, per entity: how many rows, how many bytes are reclaimable, and when
     * the retention sweep becomes entitled to take them. `purgeable` marks the entities `purge()`
     * can actually reach.
     */
    public function list(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Permanently delete trashed records, ignoring the retention window. Pass `entities` to narrow
     * to specific entity keys; omit it to empty everything self-service can reach. Records held for
     * statutory reasons (invoices, contracts, users, ...) are never reachable here.
     *
     * This cannot be undone.
     */
    public function purge(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\EnlivyObject;
use Enlivy\Service\AbstractService;
use Enlivy\Util\RequestOptions;

/**
 * Service for reading and writing per-organization setting localizations.
 *
 * A slot is addressed by a `{group}/{key}` pair and holds a language map. It is
 * not a record resource — reads and writes return the raw value map.
 */
class SettingLocalizationService extends AbstractService
{
    protected const string RESOURCE = 'setting-localizations';

    /**
     * All localization slots, keyed by group then key. Pass `group` to narrow
     * to a single group.
     */
    public function list(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $group, string $key, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$group}/{$key}"), $params, $opts);
    }

    /**
     * Create or replace a slot. Pass the language map under `value`; send an
     * empty or null `value` to clear it.
     */
    public function set(string $group, string $key, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$group}/{$key}"), $params, $opts);
    }

    public function delete(string $group, string $key, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$group}/{$key}"), $params, $opts);
    }
}

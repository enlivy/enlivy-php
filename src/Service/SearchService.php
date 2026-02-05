<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

class SearchService extends AbstractService
{
    protected const string RESOURCE = 'search';

    public function query(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}

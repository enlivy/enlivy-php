<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

trait HasImportColumnDetection
{
    /**
     * Proposes `field_position_*` values from a CSV header row. Send `headers`
     * in file order; positions come back 1-based.
     */
    public function importDetectColumns(array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, static::RESOURCE . '/imports/detect-columns'), $params, $opts);
    }
}

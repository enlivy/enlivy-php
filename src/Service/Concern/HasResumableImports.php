<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

trait HasResumableImports
{
    /**
     * Starts a new run from `summary_json.resume_from_row`. Rejected unless the
     * source run stopped for a reason a second pass can clear — check
     * `summary_json.is_resumable` first.
     */
    public function importResume(string $importId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, static::RESOURCE . "/imports/{$importId}/resume"), $params, $opts);
    }
}

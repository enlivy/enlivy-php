<?php

declare(strict_types=1);

namespace Enlivy\Service\Concern;

use Enlivy\Util\RequestOptions;

trait HasDownload
{
    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, static::RESOURCE . "/{$id}/download"), $params, $opts);
    }
}

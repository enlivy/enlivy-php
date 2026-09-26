<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Helpdesk;

use Enlivy\Organization\HelpdeskSettings;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Desk-wide defaults, one row per organization. Reading it creates it, so there is no id and it
 * never answers a 404. An inbox that sets the same field wins over what is here.
 */
class HelpdeskSettingsService extends AbstractService
{
    use HasIncludes;

    protected const string RESOURCE = 'helpdesk/settings';
    protected const ?string RESOURCE_CLASS = HelpdeskSettings::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'branding_logo_file',
        'branding_icon_file',
        'inbox_defaults',
    ];

    public function retrieve(array $params = [], ?RequestOptions $opts = null): HelpdeskSettings
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskSettings */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(array $params, ?RequestOptions $opts = null): HelpdeskSettings
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var HelpdeskSettings */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_user_id
 * @property bool $is_default
 * @property bool $is_active_sender
 * @property string $name
 * @property string|null $service
 * @property bool $has_credentials
 * @property bool $receives
 * @property array|null $settings
 * @property string|null $account_identifier
 * @property string|null $site_url
 * @property array|null $supports
 * @property string|null $synced_since_at
 * @property string|null $last_synced_at
 * @property string $created_at
 * @property string $updated_at
 */
class ApiCredential extends ApiResource
{
    public const ?string OBJECT_NAME = 'api_credential';
}

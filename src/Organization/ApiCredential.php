<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an API Credential in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $name
 * @property string|null $service
 * @property bool $has_credentials
 * @property array|null $settings
 * @property string|null $account_identifier
 * @property string|null $site_url
 * @property array|null $supports
 * @property string $created_at
 * @property string $updated_at
 */
class ApiCredential extends ApiResource
{
    public const ?string OBJECT_NAME = 'api_credential';
}

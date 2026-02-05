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
 * @property string $type
 * @property array|null $credentials
 * @property array|null $settings
 * @property string $created_at
 * @property string $updated_at
 */
class ApiCredential extends ApiResource
{
    public const ?string OBJECT_NAME = 'api_credential';
}

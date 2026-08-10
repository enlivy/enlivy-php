<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string|null $organization_id
 * @property string $source
 * @property string $type
 * @property string $value
 * @property string $normalized_value
 * @property string|null $reason
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class BlockedIdentifier extends ApiResource
{
    public const ?string OBJECT_NAME = 'blocked_identifier';
}

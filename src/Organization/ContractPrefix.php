<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract Prefix in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property float $current_number
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ContractPrefix extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_prefix';
}

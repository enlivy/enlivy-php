<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
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
 */
class ContractPrefix extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_prefix';
}

<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an entity referencing a Contract in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $entity
 * @property string|null $title
 * @property string|null $status
 * @property string|null $total
 * @property string|null $currency
 * @property string $created_at
 * @property string $updated_at
 */
class ContractConnection extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_connection';
}

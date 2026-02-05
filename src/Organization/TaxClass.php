<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Class in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property string $created_at
 * @property string $updated_at
 */
class TaxClass extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_class';
}

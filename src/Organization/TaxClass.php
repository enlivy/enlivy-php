<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Class in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $alias
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxClass extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_class';
}

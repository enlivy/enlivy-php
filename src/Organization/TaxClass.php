<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property string|null $display_name
 * @property array|null $display_name_lang_map
 * @property string|null $tax_category
 * @property string|null $auto_imported_from
 * @property string|null $auto_imported_hash
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxClass extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_class';
}

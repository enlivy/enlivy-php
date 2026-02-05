<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Project in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $alias
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property array|null $locale_list
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Project extends ApiResource
{
    public const ?string OBJECT_NAME = 'project';
}

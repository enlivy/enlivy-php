<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Project in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property array $title_lang_map
 * @property array $description_lang_map
 * @property array $locale_list
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Project extends ApiResource
{
    public const ?string OBJECT_NAME = 'project';
}

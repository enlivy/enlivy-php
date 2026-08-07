<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $organization_owner_user_id
 * @property string $title
 * @property string|null $description
 * @property array|null $organization_target_entities
 * @property string|null $content
 * @property string $locale
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_by_user_id
 * @property array|null $organization_user_abilities_by_project_id
 * @property array|null $allowed_for_organization_project_ids
 */
class Guideline extends ApiResource
{
    public const ?string OBJECT_NAME = 'guideline';
}

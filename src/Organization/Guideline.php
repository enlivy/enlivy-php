<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Guideline in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $organization_owner_user_id
 * @property string $title
 * @property string|null $content
 * @property string $locale
 * @property array|null $organization_target_entities
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Guideline extends ApiResource
{
    public const ?string OBJECT_NAME = 'guideline';
}

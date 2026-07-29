<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Report Schema in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string|null $type
 * @property string|null $title
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property array|null $instructions_lang_map
 * @property string|null $frequency
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property array|null $organization_user_abilities_by_project_id
 * @property array|null $allowed_for_organization_project_ids
 */
class ReportSchema extends ApiResource
{
    public const ?string OBJECT_NAME = 'report_schema';
}

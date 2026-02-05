<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Report Schema in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_user_role_id
 * @property string|null $type
 * @property string|null $title
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property array|null $instructions_lang_map
 * @property string|null $frequency
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ReportSchema extends ApiResource
{
    public const ?string OBJECT_NAME = 'report_schema';
}

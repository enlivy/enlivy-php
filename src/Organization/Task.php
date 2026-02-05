<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Task in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $parent_organization_task_id
 * @property string|null $organization_task_status_id
 * @property string|null $organization_project_id
 * @property string|null $organization_report_schema_id
 * @property string|null $organization_report_id
 * @property string|null $assigned_by_organization_user_id
 * @property string|null $assigned_to_organization_user_id
 * @property string|null $completed_by_organization_user_id
 * @property string|null $title
 * @property string|null $content
 * @property array|null $title_lang_map
 * @property array|null $content_lang_map
 * @property bool $has_lang_map
 * @property string|null $due_at
 * @property string|null $completed_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Task extends ApiResource
{
    public const ?string OBJECT_NAME = 'task';
}

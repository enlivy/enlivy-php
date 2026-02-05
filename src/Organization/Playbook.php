<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Playbook in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_playbook_id
 * @property string|null $organization_project_id
 * @property string|null $procedure_organization_owner_user_id
 * @property string $title
 * @property string|null $description
 * @property string $locale
 * @property string|null $procedure_video_url
 * @property string|null $procedure_start_point
 * @property array|null $procedure_start_point_inputs
 * @property array|null $procedure_process_steps
 * @property array|null $procedure_process_checklist
 * @property string|null $procedure_end_point
 * @property array|null $procedure_end_point_outputs
 * @property array|null $procedure_best_practices
 * @property array|null $procedure_faqs
 * @property array|null $procedure_files
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Playbook extends ApiResource
{
    public const ?string OBJECT_NAME = 'playbook';
}

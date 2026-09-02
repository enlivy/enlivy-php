<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_prospect_pipeline_id
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property string|null $stage_type
 * @property string|null $rgba_color_code
 * @property int $order
 * @property int|null $is_stuck_threshold_days
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ProspectStage extends ApiResource
{
    public const ?string OBJECT_NAME = 'prospect_stage';
}

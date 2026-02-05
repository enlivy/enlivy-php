<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Report Schema Field in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_report_schema_id
 * @property string|null $title
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property array|null $options_lang_map
 * @property array|null $instructions_lang_map
 * @property string $type
 * @property string|null $default
 * @property bool $is_required
 * @property int $order
 * @property array|null $style_settings
 * @property array|null $conditional_logic_settings
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ReportSchemaField extends ApiResource
{
    public const ?string OBJECT_NAME = 'report_schema_field';
}

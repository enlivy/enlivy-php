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
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property string|null $locale
 * @property array|null $locale_list
 * @property string|null $default_organization_prospect_status_id
 * @property string|null $default_inbound_organization_prospect_status_id
 * @property array|null $custom_inbound_success_title_lang_map
 * @property array|null $custom_inbound_success_message_lang_map
 * @property array|null $custom_inbound_success_actions
 * @property string|null $deleted_by_user_id
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class Project extends ApiResource
{
    public const ?string OBJECT_NAME = 'project';
}

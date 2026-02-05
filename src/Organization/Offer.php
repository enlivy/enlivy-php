<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Offer in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $created_by_user_id
 * @property string|null $expired_by_user_id
 * @property string|null $contract_default_sender_user_id
 * @property string|null $alias
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property string|null $locale
 * @property array|null $locale_list
 * @property bool $is_active
 * @property int|null $proposal_valid_for_seconds
 * @property array|null $allowed_payment_methods
 * @property bool $contract_is_required
 * @property string|null $contract_trigger
 * @property string|null $expires_at
 * @property string|null $expired_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Offer extends ApiResource
{
    public const ?string OBJECT_NAME = 'offer';
}

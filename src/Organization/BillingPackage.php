<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Billing Package in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $created_by_user_id
 * @property string|null $expired_by_user_id
 * @property string|null $alias
 * @property array|null $name_lang_map
 * @property array|null $description_lang_map
 * @property string|null $locale
 * @property array|null $locale_list
 * @property string|null $type
 * @property bool $is_active
 * @property bool $customer_can_reconfigure
 * @property bool $customer_can_cancel
 * @property bool $customer_can_pause
 * @property string|null $portal_discovery_mode
 * @property string|null $portal_url
 * @property bool $is_expired
 * @property bool $is_available
 * @property int|null $proposal_valid_for_seconds
 * @property array|null $allowed_payment_methods
 * @property array|null $available_currencies
 * @property string|null $currency_conversion_fee
 * @property string|null $expires_at
 * @property string|null $expired_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class BillingPackage extends ApiResource
{
    public const ?string OBJECT_NAME = 'billing_package';
}

<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Organization in the Enlivy API.
 *
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property string $country_code
 * @property string $timezone
 * @property string $locale
 * @property string $currency
 * @property string $membership_plan
 * @property array $information
 * @property array $feature_list
 * @property array $branding_map
 * @property string $created_at
 * @property string $updated_at
 */
class Organization extends ApiResource
{
    public const ?string OBJECT_NAME = 'organization';
}

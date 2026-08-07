<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * @property string $id
 * @property string $object
 * @property string $user_id
 * @property string|null $organization_id
 * @property string $environment
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property string $country_code
 * @property string $timezone
 * @property string|null $address_line_1
 * @property string|null $address_line_2
 * @property string|null $address_city
 * @property string|null $address_county
 * @property string|null $address_state
 * @property string|null $address_iso_3166
 * @property string|null $address_zip_code
 * @property string $locale
 * @property array|null $locale_list
 * @property string $currency
 * @property array|null $currency_list
 * @property array $information
 * @property array $feature_list
 * @property string|null $accounting_email_address
 * @property string|null $accounting_contact_phone
 * @property string|null $accounting_contact_phone_country_code
 * @property array $branding_map
 * @property string|null $branding_icon_organization_file_id
 * @property string|null $branding_logo_organization_file_id
 * @property string|null $user_portal_domain
 * @property string|null $user_portal_domain_status
 * @property string|null $customer_portal_base_url
 * @property array $integrations
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Organization extends ApiResource
{
    public const ?string OBJECT_NAME = 'organization';
}

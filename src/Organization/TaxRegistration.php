<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Registration in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $country_code
 * @property string|null $subdivision_iso_3166
 * @property string $scheme
 * @property string|null $registration_number
 * @property string|null $filing_frequency
 * @property string|null $cash_accounting_from
 * @property string|null $cash_accounting_to
 * @property string|null $exemption_vatex_code
 * @property array|null $exemption_reason_lang_map
 * @property string $effective_from
 * @property string|null $effective_to
 * @property string|null $validated_at
 * @property string|null $validation_source
 * @property string|null $validation_reference
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class TaxRegistration extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_registration';
}

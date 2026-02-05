<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Filing Jurisdiction in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $alias
 * @property string|null $country_code
 * @property array|null $note_lang_map
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class TaxFilingJurisdiction extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_filing_jurisdiction';
}

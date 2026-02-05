<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Tax Filing Jurisdiction in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $country_code
 * @property string|null $region
 * @property string $created_at
 * @property string $updated_at
 */
class TaxFilingJurisdiction extends ApiResource
{
    public const ?string OBJECT_NAME = 'tax_filing_jurisdiction';
}

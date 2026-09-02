<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_employment_id
 * @property string|null $axis
 * @property string|null $jurisdiction_code
 * @property string|null $jurisdiction_subdivision_iso_3166
 * @property string|null $effective_from
 * @property string|null $effective_to
 * @property string|null $source
 * @property string|null $source_reference
 * @property string $created_at
 * @property string $updated_at
 */
class EmploymentJurisdiction extends ApiResource
{
    public const ?string OBJECT_NAME = 'employment_jurisdiction';
}

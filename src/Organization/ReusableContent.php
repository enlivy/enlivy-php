<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Reusable Content in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property string|null $scope
 * @property string $entity_type
 * @property array|null $configuration
 * @property string $created_at
 * @property string $updated_at
 */
class ReusableContent extends ApiResource
{
    public const ?string OBJECT_NAME = 'reusable_content';
}
